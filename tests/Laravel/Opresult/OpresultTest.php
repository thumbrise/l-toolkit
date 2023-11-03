<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Opresult;

use Illuminate\Support\Facades\Route;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

class OpresultTest extends TestCase
{
    /**
     * @test
     */
    public function httpResponseCodeValid()
    {
        $status = 409;

        Route::get('test', function () use ($status) {
            return OperationResult::success('ok')->withHttpCode($status);
        });

        $response = $this->get('test');

        $response->assertJsonPath('data', 'ok');
        $response->assertStatus($status);
    }

    /**
     * @test
     */
    public function httpResponseHeadersValid()
    {
        $headerKey = 'X-My-Test-Header';
        $headerValue = 'Super value!';

        Route::get('test', function () use ($headerKey, $headerValue) {
            return OperationResult::success('ok')->withHttpHeaders([$headerKey => $headerValue]);
        });

        $response = $this->get('test');

        $response->assertJsonPath('data', 'ok');
        $response->assertHeader($headerKey, $headerValue);
    }
}