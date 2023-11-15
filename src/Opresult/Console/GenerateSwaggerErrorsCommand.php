<?php

namespace Thumbrise\Toolkit\Opresult\Console;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Thumbrise\Toolkit\Opresult\Ast\UsagesSearcher;

class GenerateSwaggerErrorsCommand extends Command
{
    protected $description = 'Анализирует код и генерирует аттрибуты ответов с ошибками';

    protected $signature = 'opresult:generate-swagger-errors';

    public function __construct(private readonly UsagesSearcher $searcher)
    {
        parent::__construct();
    }

    public function handle()
    {
        $controllersPath = app_path('Http/Controllers');

        $filePaths = $this->scanDirRecursive($controllersPath);
        $this->searcher->search($filePaths[1]);
        return;
        foreach ($filePaths as $filePath) {
            $this->searcher->search($filePath);
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