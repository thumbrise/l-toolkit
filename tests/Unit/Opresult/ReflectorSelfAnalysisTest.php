<?php

namespace Thumbrise\Toolkit\Tests\Unit\Opresult;

use PHPUnit\Framework\TestCase;
use Thumbrise\Toolkit\Opresult\Internal\Reflector;

/**
 * !!! При модификации теста, стоит обратить внимание на переменные $line, $function, $class TODO:(Устаревшее, удалить).
 *
 * @internal
 */
class ReflectorSelfAnalysisTest extends TestCase
{
    /**
     * @test
     */
    public function selfAnalysis()
    {
        $expected = __FILE__.':'.(__LINE__ + 2);

        $result = Reflector::getCallInfo([['class' => Reflector::class, 'function' => 'getCallInfo']]);

        $actual = $result['where'];
        $this->assertEquals($expected, $actual);
    }
}
