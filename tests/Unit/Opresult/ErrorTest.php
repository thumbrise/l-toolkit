<?php

namespace Thumbrise\Toolkit\Tests\Unit\Opresult;

use PHPUnit\Framework\TestCase;
use Thumbrise\Toolkit\Opresult\Error;

class ErrorTest extends TestCase
{
    /**
     * @test
     */
    public function notStringMessage()
    {
        Error::make(['sine' => 'asd']);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function withoutContext()
    {
        $code2 = 'Какой то внутренний код уровня 2';
        $error2 = Error::make('Что то пошло не так на уровне 2', $code2);

        $code1 = 'Конечный код';
        $error1 = $error2->wrap('И правда что-то не так', $code1)->withoutContext();

        $errorArray = $error1->toArray();
        $this->assertArrayHasKey('error_code', $errorArray);
        $this->assertEquals($code1, $errorArray['error_code']);
        $this->assertArrayNotHasKey('error_context', $errorArray);
        $this->assertArrayHasKey('error_previous', $errorArray);
        $this->assertArrayNotHasKey('error_context', $errorArray['error_previous']);
    }

    /**
     * @test
     */
    public function withoutPrevious()
    {
        $code2 = 'Какой то внутренний код уровня 2';
        $error2 = Error::make('Что то пошло не так на уровне 2', $code2);

        $code1 = 'Конечный код';
        $error1 = $error2->wrap('И правда что-то не так', $code1)->withoutPrevious();

        $errorArray = $error1->toArray();
        $this->assertArrayHasKey('error_code', $errorArray);
        $this->assertEquals($code1, $errorArray['error_code']);
        $this->assertArrayNotHasKey('error_previous', $errorArray);
    }

    /**
     * @test
     */
    public function wrap()
    {
        $code3 = 'Какой то внутренний код уровня 3';
        $error3 = Error::make('Что то пошло не так на уровне 3', $code3);

        $code2 = 'Какой то внутренний код уровня 2';
        $error2 = $error3->wrap('Что то пошло не так на уровне 2', $code2);

        $code1 = 'Конечный код';
        $error1 = $error2->wrap('И правда что-то не так', $code1);


        $this->assertTrue($error1->is($code1));
        $this->assertTrue($error1->is($code2));
        $this->assertTrue($error1->is($code3));
    }
}