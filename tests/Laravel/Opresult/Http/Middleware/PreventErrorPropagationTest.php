<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Opresult\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Thumbrise\Toolkit\Opresult\Http\Middleware\PreventErrorPropagation;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

class PreventErrorPropagationTest extends TestCase
{


    /**
     * @test
     */
    public function isWork()
    {
        if (! $this->app->environment('testing')) {
            $this->markTestSkipped('Окружение должно быть testing');
        }

        Route::middleware(PreventErrorPropagation::class.':testing')
            ->get('/api/test', function () {
                return OperationResult::error('deep error', 'deep_error')
                    ->withError('client error', 'client_error');
            });

        $response = $this->get('/api/test');

        $response->assertOk();
        $response->assertJsonMissingPath('error_previous');
        $response->assertJsonMissingPath('error_previous');
        $response->assertJsonMissingPath('errorPrevious');
        $response->assertJsonMissingPath('errorContext');
    }


}
