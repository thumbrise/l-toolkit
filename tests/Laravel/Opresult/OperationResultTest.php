<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Opresult;

use Illuminate\Support\Facades\Route;
use Thumbrise\Toolkit\Opresult\Error;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Opresult\Validator;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

class OperationResultTest extends TestCase
{


    /**
     * @test
     */
    public function errorIsValid()
    {
        $errorMessage = 'some error';
        $errorCode    = 'VALIDATION';
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
        $headerKey   = 'X-My-Test-Header';
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
        $headerKey   = 'X-My-Test-Header';
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
    public function properlyContextByErrorMake()
    {
        $expected = __FILE__.':'.(__LINE__ + 3);


        $v = Error::make();


        $result = $v->toArray();
        $this->assertArrayHasKey('error_context', $result);
        $actual = $result['error_context']['where'];
        $this->assertEquals($expected, $actual);
    }


    /**
     * @test
     */
    public function properlyContextByErrorWrap()
    {
        $expected = __FILE__.':'.(__LINE__ + 4);


        $initialError = Error::make();
        $v            = $initialError->wrap();


        $result = $v->toArray();
        $this->assertArrayHasKey('error_context', $result);
        $actual = $result['error_context']['where'];
        $this->assertEquals($expected, $actual);
    }


    /**
     * @test
     */
    public function properlyContextByOpresultError()
    {
        $expected = __FILE__.':'.(__LINE__ + 3);


        $v = OperationResult::error();


        $this->assertTrue($v->isError());
        $result = $v->toArray();
        $this->assertArrayHasKey('error_context', $result);
        $actual = $result['error_context']['where'];
        $this->assertEquals($expected, $actual);
    }


    /**
     * @test
     */
    public function properlyContextByValidate()
    {
        $expected = __FILE__.':'.(__LINE__ + 3);


        $v = Validator::validate(['name' => 15], ['name' => ['string']]);


        $this->assertTrue($v->isError());
        $result = $v->toArray();
        $this->assertArrayHasKey('error_context', $result);
        $actual = $result['error_context']['where'];
        $this->assertEquals($expected, $actual);
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
