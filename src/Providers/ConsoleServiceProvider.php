<?php

namespace Thumbrise\Toolkit\Providers;

use Illuminate\Support\ServiceProvider;
use Thumbrise\Toolkit\Opresult\Console\GenerateSwaggerErrorsCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                GenerateSwaggerErrorsCommand::class,
            ]);
        }
    }

}