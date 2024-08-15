<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Opresult;

use Illuminate\Support\Facades\Route;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use Thumbrise\Toolkit\Opresult\Error;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Opresult\Validator;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

/**
 * @internal
 */
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
            return OperationResult::success('ok')->setStatusCode($status);
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
            return OperationResult::success('ok')->setStatusCode($status);
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
            return OperationResult::success('ok')->withHeaders([$headerKey => $headerValue]);
        });

        $response = $this->get('test');

        $response->assertJsonPath('data', 'ok');
        $response->assertHeader($headerKey, $headerValue);
    }

    #[Test]
    public function properlyAddDefaultParams()
    {
        $expectedMessage  = 'Validation error.';
        $expectedCode     = 'ErrorsBasic/Validation';
        $expectedHttpCode = 422;

        $v = Validator::validate([], ['some' => ['string', 'required']]);

        $this->assertSame($expectedHttpCode, $v->getStatusCode());
        $json = new AssertableJsonString($v->toArray());
        $json->assertPath('error_message', $expectedMessage);
        $json->assertPath('error_code', $expectedCode);
    }

    #[Test]
    public function properlyAddGlobalParams()
    {
        $expectedMessage  = 'Arguments error.';
        $expectedCode     = 'ErrorsBasic/BadArguments';
        $expectedHttpCode = 400;
        Validator::globalMessage($expectedMessage);
        Validator::globalCode($expectedCode);
        Validator::globalHttpCode($expectedHttpCode);

        $v = Validator::validate([], ['some' => ['string', 'required']]);

        $this->assertSame($expectedHttpCode, $v->getStatusCode());
        $json = new AssertableJsonString($v->toArray());
        $json->assertPath('error_message', $expectedMessage);
        $json->assertPath('error_code', $expectedCode);
    }

    #[Test]
    public function properlyAddMessageTranslationParam()
    {
        $expectedMessage           = 'Validation error.';
        $expectedMessageTranslated = 'Ошибка валидации.';

        /** @var Translator $translator */
        $translator = $this->app['translator'];
        $locale     = 'ru';
        $translator->setLocale($locale);
        $translator->addLines([
            $expectedMessage => $expectedMessageTranslated,
        ], $locale);
        Validator::globalMessage($expectedMessage);

        $v = Validator::validate([], ['some' => ['string', 'required']]);

        $json = new AssertableJsonString($v->toArray());
        $json->assertPath('error_message', $expectedMessageTranslated);
    }

    #[Test]
    public function properlyAddValidationFields()
    {
        $v = Validator::validate([], ['some' => ['string', 'required']]);

        $json = new AssertableJsonString($v->toArray());
        $json->assertStructure([
            'error_message',
            'error_code',
            'error_fields',
            'error_context',
        ]);
        $json->assertMissing(['data']);
        $json->assertCount(1, 'error_fields');
    }

    /**
     * @test
     */
    public function properlyContextByErrorMake()
    {
        $expected = __FILE__.':'.(__LINE__ + 2);

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
        $expected = __FILE__.':'.(__LINE__ + 3);

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
        $expected = __FILE__.':'.(__LINE__ + 2);

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
        $expected = __FILE__.':'.(__LINE__ + 2);

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
