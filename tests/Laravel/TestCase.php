<?php

namespace Thumbrise\Toolkit\Tests\Laravel;


use Thumbrise\Toolkit\Providers\ConsoleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getBasePath()
    {
        return realpath(__DIR__ . '/Skeleton');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app): array
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }
}
