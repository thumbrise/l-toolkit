<?php

namespace Thumbrise\Toolkit\Opresult\Generate;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use Thumbrise\Toolkit\Opresult\Generate\Internal\FindOpresultsWithError;
use Thumbrise\Toolkit\Opresult\Generate\Internal\ReplaceLine;

class SwaggerErrorAttributesGenerator
{
    public function __construct(
        private readonly FindOpresultsWithError $findOpresultsWithError,
        private readonly ReplaceLine            $replaceLine,
    )
    {
    }

    public function generate(string $filepath): void
    {
        $opresults = $this->findOpresultsWithError->do($filepath);
        $content = <<<CONTENT

            new \OpenApi\Attributes\Response(response: 200, description: 'Ошибка', content: new \OpenApi\Attributes\JsonContent(default: array('errorMessage' => 'error 1', 'errorCode' => 'VALIDATION'))),
            new \OpenApi\Attributes\Response(response: 200, description: 'Ошибка', content: new \OpenApi\Attributes\JsonContent(default: array('errorMessage' => 'error 1', 'errorCode' => 'UNAUTHENTICATED'))),
            new \OpenApi\Attributes\Response(response: 200, description: 'Ошибка', content: new \OpenApi\Attributes\JsonContent(default: array('errorMessage' => 'LOL', 'errorCode' => 'KEK'))),


CONTENT;

        $this->replaceLine->do($filepath, 19, $content);

        $builder = new BuilderFactory();

        $responseAttributes = [];
        foreach ($opresults as $opresult) {
            $default = [
                'errorMessage' => $opresult->error->message(),
                'errorCode' => $opresult->error->code(),
            ];
            $content = $builder->new('\\' . JsonContent::class, ['default' => $default]);
            $response = $builder->new('\\' . Response::class, ['response' => 200, 'description' => 'Ошибка', 'content' => $content]);
            $responseAttributes[] = $response;
        }
        $s = new Standard();
        $lines = [];
        foreach ($responseAttributes as $responseAttribute) {
            $responseAttributeLine = $s->prettyPrint([$responseAttribute]);
            $responseAttributeLine = $responseAttributeLine . ',';
            $lines[] = $responseAttributeLine;
        }
        $resultText = join(PHP_EOL, $lines);
        //TODO Нужна логика проверки существования нужных аттрибутов в коде, для того чтобы не дублировать или не писать их заново
        echo $resultText;
    }


}