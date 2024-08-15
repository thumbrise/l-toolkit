<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Common\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Thumbrise\Toolkit\Common\Http\Middleware\RequestToCamel;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

/**
 * @internal
 */
class RequestToCamelTest extends TestCase
{
    /**
     * @test
     */
    public function isWork()
    {
        Route::middleware(RequestToCamel::class)
            ->get('/api/test-get', function (Request $request) {
                return response()->json($request->input());
            })
        ;

        $response = $this->get('/api/test-get?some_key=okOK-ok_OK');

        $response->assertOk();
        $response->assertJson(['someKey' => 'okOK-ok_OK']);

        Route::middleware(RequestToCamel::class)
            ->post('/api/test-post', function (Request $request) {
                return response()->json($request->input());
            })
        ;

        $response = $this->post('/api/test-post', [
            'some_key' => 'okOK-ok_OK',
        ]);

        $response->assertOk();
        $response->assertJson(['someKey' => 'okOK-ok_OK']);
    }

    /**
     * @test
     */
    public function isWorkWithEmptyRequestAndEmptyResponse()
    {
        Route::middleware(RequestToCamel::class)
            ->get('/api/test', function () {})
        ;

        $response = $this->get('/api/test');

        $response->assertOk();
    }
}
