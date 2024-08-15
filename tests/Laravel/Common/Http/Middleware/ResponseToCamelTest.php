<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Common\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Thumbrise\Toolkit\Common\Http\Middleware\ResponseToCamel;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

/**
 * @internal
 */
class ResponseToCamelTest extends TestCase
{
    /**
     * @test
     */
    public function isWork()
    {
        Route::middleware(ResponseToCamel::class)
            ->get('/api/test', function () {
                return response()->json([
                    'some_key' => 'blabla',
                ]);
            })
        ;

        $response = $this->get('/api/test');

        $response->assertOk();
        $response->assertJson(['someKey' => 'blabla']);
    }

    /**
     * @test
     */
    public function isWorkWithEmptyResponse()
    {
        Route::middleware(ResponseToCamel::class)
            ->get('/api/test', function () {})
        ;

        $response = $this->get('/api/test');

        $response->assertOk();
    }
}
