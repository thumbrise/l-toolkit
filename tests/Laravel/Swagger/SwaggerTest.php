<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Swagger;

use Thumbrise\Toolkit\Opresult\Console\GenerateSwaggerErrorsCommand;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

class SwaggerTest extends TestCase
{
    /**
     * @test
     */
    public function valid()
    {
        $this->artisan(GenerateSwaggerErrorsCommand::class);
    }
}