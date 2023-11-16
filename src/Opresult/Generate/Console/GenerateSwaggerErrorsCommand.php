<?php

namespace Thumbrise\Toolkit\Opresult\Generate\Console;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Thumbrise\Toolkit\Opresult\Generate\SwaggerErrorAttributesGenerator;

class GenerateSwaggerErrorsCommand extends Command
{
    protected $description = 'Анализирует код и генерирует аттрибуты ответов с ошибками';

    protected $signature = 'opresult:generate-swagger-errors';

    public function __construct(private readonly SwaggerErrorAttributesGenerator $generator)
    {
        parent::__construct();
    }

    public function handle()
    {
        $controllersPath = app_path('Http/Controllers');

        $filePaths = $this->scanDirRecursive($controllersPath);
        $this->generator->generate($filePaths[1]);
        return;
        foreach ($filePaths as $filePath) {
            $this->generator->generate($filePath);
            var_dump('00000000000000000000000000');
        }
    }

    private function scanDirRecursive($path)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $files = [];
        foreach ($rii as $file)
            if (! $file->isDir())
                $files[] = $file->getPathname();

        return $files;
    }

}