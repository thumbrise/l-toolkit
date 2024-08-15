<?php

namespace Thumbrise\Toolkit\Tests\Unit\Opresult;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Unit\Opresult\Stubs\StubError;

class OperationResultTest extends TestCase
{


    #[Test]
    public function properlyAddAdditional()
    {
        $additional = [
            'something' => [
                'some1' => 1,
                'some2' => 2,
            ],
        ];
        $expected   = [
            'error_message' => 'error',
            'error_code'    => 'error',
            ...$additional
        ];



        $v = OperationResult::error('error', 'error', $additional)->withoutErrorContext();

        $actual = $v->toArray();

        $this->assertTrue($v->isError());
        $this->assertSame($expected, $actual);
    }


    #[Test]
    public function properlyAddEnumPrefixInCode()
    {
        $expected = [
            'error_message' => 'Some error',
            'error_code'    => 'StubError/StubCase',
        ];


        $v      = OperationResult::error('Some error', StubError::StubCase)->withoutErrorContext();
        $actual = $v->toArray();

        $this->assertTrue($v->isError());
        $this->assertSame($expected, $actual);
    }


    /**
     * @test
     */
    public function withError()
    {
        $code3   = 'Какой то внутренний код уровня 3';
        $result3 = OperationResult::error('Что то пошло не так на уровне 3', $code3);

        $code2   = 'Какой то внутренний код уровня 2';
        $result2 = $result3->withError('Что то пошло не так на уровне 2', $code2);

        $code1   = 'Конечный код';
        $result1 = $result2->withError('И правда что-то не так', $code1);


        $this->assertTrue($result1->isError($code1));
        $this->assertTrue($result1->isError($code2));
        $this->assertTrue($result1->isError($code3));
    }


    /**
     * @test
     */
    public function withLastErrorOnly()
    {
        $code2   = 'Какой то внутренний код уровня 2';
        $result2 = OperationResult::error('Что то пошло не так на уровне 2', $code2);

        $code1   = 'Конечный код';
        $result1 = $result2->withError('И правда что-то не так', $code1)->withLastErrorOnly();


        $this->assertNotNull($result1->error);
        $errorArray = $result1->error->toArray();
        $this->assertArrayHasKey('error_code', $errorArray);
        $this->assertEquals($code1, $errorArray['error_code']);
        $this->assertArrayNotHasKey('error_previous', $errorArray);
    }


    /**
     * @test
     */
    public function withoutErrorContext()
    {
        $code2   = 'Какой то внутренний код уровня 2';
        $result2 = OperationResult::error('Что то пошло не так на уровне 2', $code2);

        $code1   = 'Конечный код';
        $result1 = $result2->withError('И правда что-то не так', $code1)->withoutErrorContext();


        $this->assertNotNull($result1->error);
        $errorArray = $result1->error->toArray();
        $this->assertArrayHasKey('error_code', $errorArray);
        $this->assertEquals($code1, $errorArray['error_code']);
        $this->assertArrayNotHasKey('error_context', $errorArray);
        $this->assertArrayHasKey('error_previous', $errorArray);
        $this->assertArrayNotHasKey('error_context', $errorArray['error_previous']);
    }


}
