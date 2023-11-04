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
    public function errorIsValid()
    {
        $errorMessage = 'some error';
        $errorCode = 'VALIDATION';
        Route::get('test', function () use ($errorMessage, $errorCode) {
            return OperationResult::error($errorMessage, $errorCode);
        });


        $response = $this->getJson('test');


        $response->assertJsonMissingPath('data');
        $response->assertJsonPath('error_message', $errorMessage);
        $response->assertJsonPath('error_code', $errorCode);
    }

    /**
     * @test
     */
    public function httpMagicMethodResponseCodeValid()
    {
        $status = 409;
        Route::get('test', function () use ($status) {
            return OperationResult::success('ok')->json(status: $status);
        });


        $response = $this->get('test');


        $response->assertJsonPath('data', 'ok');
        $response->assertStatus($status);
    }

    /**
     * @test
     */
    public function httpMagicMethodResponseHeadersValid()
    {
        $headerKey = 'X-My-Test-Header';
        $headerValue = 'Super value!';
        Route::get('test', function () use ($headerKey, $headerValue) {
            return OperationResult::success('ok')->withHeaders([$headerKey => $headerValue]);
        });


        $response = $this->get('test');


        $response->assertJsonPath('data', 'ok');
        $response->assertHeader($headerKey, $headerValue);
    }

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

    /**
     * @test
     */
    public function successIsValid()
    {
        Route::get('test', function () {
            return OperationResult::success('ok');
        });


        $response = $this->getJson('test');


        $response->assertJsonPath('data', 'ok');
    }
}