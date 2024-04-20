<?php

namespace Thumbrise\Toolkit\Tests\Unit\Util;

use Thumbrise\Toolkit\Tests\Unit\TestCase;
use Thumbrise\Toolkit\Util\CommonUtil;

class CommonUtilTest extends TestCase
{


    public function testFirstNotEmpty()
    {
        $this->assertEquals('John', CommonUtil::firstNotEmpty('', 'John', null, 'Doe'));
        $this->assertEquals('Jane', CommonUtil::firstNotEmpty('', null, 'Jane', 'Doe'));
        $this->assertEquals(42, CommonUtil::firstNotEmpty(0, false, null, 42));
        $this->assertNull(CommonUtil::firstNotEmpty('', null, false, null));
    }


}
