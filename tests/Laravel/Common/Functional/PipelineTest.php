<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Common\Functional;

use Thumbrise\Toolkit\Common\Functional\Pipeline;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

class PipelineTest extends TestCase
{
    public function testProcess()
    {
        $input = 'initial data';
        $addit1 = ' - operation 1';
        $addit2 = ' - operation 2';
        $expected = $input . $addit1 . $addit2;


        $actual = Pipeline::process($input, [
            function ($passable) use ($addit1) {
                return $passable . $addit1;
            },
            function ($passable) use ($addit2) {
                return $passable . $addit2;
            },
        ]);


        $this->assertEquals($expected, $actual);
    }
}