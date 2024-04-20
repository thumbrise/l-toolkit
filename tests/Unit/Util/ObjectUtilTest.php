<?php

namespace Thumbrise\Toolkit\Tests\Unit\Util;

use Thumbrise\Toolkit\Tests\Unit\TestCase;
use Thumbrise\Toolkit\Util\ObjectUtil;

class ObjectUtilTest extends TestCase
{


    public function testMapByKeys()
    {
        $from = (object)[
            'name' => 'John',
            'age'  => 30,
            'city' => 'New York',
        ];

        $to = (object)[
            'fullName' => '',
            'yearsOld' => 0,
            'location' => '',
        ];

        $keysFromTo = [
            'name' => 'fullName',
            'age'  => 'yearsOld',
            'city' => 'location',
        ];

        $result = ObjectUtil::mapByKeys($from, $to, $keysFromTo);

        $this->assertEquals('John', $result->fullName);
        $this->assertEquals(30, $result->yearsOld);
        $this->assertEquals('New York', $result->location);
    }


}
