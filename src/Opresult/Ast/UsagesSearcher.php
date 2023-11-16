<?php

namespace Thumbrise\Toolkit\Opresult\Ast;

use Closure;
use Exception;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Thumbrise\Toolkit\Opresult\OperationResult;

class UsagesSearcher
{

    public function search(string $filepath)
    {
        $code = file_get_contents($filepath);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $ast = $parser->parse($code);

        $nameResolver = new NameResolver(null, [
            'preserveOriginalNames' => false,
            'replaceNodes' => true,
        ]);

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);
        $ast = $nodeTraverser->traverse($ast);

        $returnsOpresultError = $this->filterNodes($ast, $this->isReturnAndIsOpresultError(...));
        //TODO Нужна логика рекурсивного поиска возвратов ошибок opresult
        $returnsUnknown = $this->filterNodes($ast, $this->isReturnAndIsUnknown(...));

        $opresults = $this->parseOpresultsFromReturns($returnsOpresultError);

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

    private function filterNodes(array $nodes, Closure $filter): array
    {
        $traverser = new NodeTraverser();
        $visitor = new FindingVisitor($filter);
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);

        return $visitor->getFoundNodes();
    }

    private function isOpresultError(Node\Stmt\Return_ $node): bool
    {
        $opresultParts = explode('\\', OperationResult::class);

        return $node->expr instanceof Node\Expr\StaticCall
               && $node->expr->class instanceof Node\Name\FullyQualified
               && $node->expr->class->getParts() == $opresultParts
               && $node->expr->name instanceof Node\Identifier
               && $node->expr->name->name === 'error';
    }

    private function isReturnAndIsOpresultError(Node $node): bool
    {
        return $node instanceof Node\Stmt\Return_ && $this->isOpresultError($node);
    }

    private function isReturnAndIsUnknown(Node $node): bool
    {
        return $node instanceof Node\Stmt\Return_ && ! $this->isOpresultError($node);
    }

    /**
     * @param \PhpParser\Node\Stmt\Return_[] $nodes
     *
     * @return \Thumbrise\Toolkit\Opresult\OperationResult[]
     * @throws \Exception
     */
    private function parseOpresultsFromReturns(array $nodes): array
    {
        $opresults = [];
        foreach ($nodes as $node) {
            if (
                ! $node->expr instanceof Node\Expr\StaticCall
            ) {
                throw new Exception('Type error');
            }
            $args = $node->expr->getArgs();
            $message = @$args[0]?->value?->value;
            $code = @$args[1]?->value?->name?->name ?? @$args[1]?->value?->value;
            $opresults[] = OperationResult::error($message, $code);
        }

        return $opresults;
    }

}