<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Common\Functional;

use Thumbrise\Toolkit\Common\Functional\Pipeline;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

/**
 * @internal
 */
class PipelineTest extends TestCase
{
    public function testProcess()
    {
        $input    = 'initial data';
        $addit1   = ' - operation 1';
        $addit2   = ' - operation 2';
        $expected = $input.$addit1.$addit2;

        $actual = Pipeline::process($input, [
            function ($passable) use ($addit1) {
                return $passable.$addit1;
            },
            function ($passable) use ($addit2) {
                return $passable.$addit2;
            },
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testProcessOperationError()
    {
        $input         = 'initial data';
        $addit1        = ' - operation 1';
        $addit2        = ' - operation 2';
        $expectedError = 'some error';

        $opresultActual = Pipeline::process($input, [
            function ($passable) use ($addit1) {
                return OperationResult::success($passable.$addit1);
            },
            function () use ($expectedError) {
                return OperationResult::error($expectedError);
            },
            function ($passable) use ($addit2) {
                return OperationResult::success($passable.$addit2);
            },
        ]);

        $this->assertInstanceOf(OperationResult::class, $opresultActual);
        $this->assertTrue($opresultActual->isError());
        $this->assertNull($opresultActual->data);
        $this->assertEquals($expectedError, $opresultActual->error->message());
    }

    public function testProcessOperationResult()
    {
        $input    = 'initial data';
        $addit1   = ' - operation 1';
        $addit2   = ' - operation 2';
        $expected = $input.$addit1.$addit2;

        $opresultActual = Pipeline::process($input, [
            function ($passable) use ($addit1) {
                return OperationResult::success($passable.$addit1);
            },
            function ($passable) use ($addit2) {
                return OperationResult::success($passable.$addit2);
            },
        ]);

        $this->assertInstanceOf(OperationResult::class, $opresultActual);
        $this->assertEquals($expected, $opresultActual->data);
    }
}
