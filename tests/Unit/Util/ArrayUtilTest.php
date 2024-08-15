<?php

namespace Thumbrise\Toolkit\Tests\Unit\Util;

use Thumbrise\Toolkit\Tests\Unit\TestCase;
use Thumbrise\Toolkit\Util\ArrayUtil;

/**
 * @internal
 */
class ArrayUtilTest extends TestCase
{
    private const DATA_MIXED = [
        'some_data1' => 'a',
        'someData2'  => 'a',
        'someArray'  => [
            'some_inner_data1' => 1,
            'some_inner_data2' => 'a',
        ],
    ];

    private const DATA_CAMEL = [
        'someData1' => 'a',
        'someData2' => 'a',
        'someArray' => [
            'someInnerData1' => 1,
            'someInnerData2' => 'a',
        ],
    ];
    private const DATA_SNAKE = [
        'some_data1' => 'a',
        'some_data2' => 'a',
        'some_array' => [
            'some_inner_data1' => 1,
            'some_inner_data2' => 'a',
        ],
    ];

    /**
     * @test
     */
    public function flatMapKeys()
    {
        $data = [
            'a'     => 1,
            'b'     => 2,
            'c.d'   => 3,
            'c.e'   => 4,
            'f.g.h' => 5,
        ];

        $keys = ['a', 'c.d', 'f.g.h'];

        $expectedResult = [
            'a' => 1,
            'd' => 3,
            'h' => 5,
        ];

        $result = ArrayUtil::flatMapKeys($data, $keys);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function flatMapKeysAssoc()
    {
        $data = [
            'a'     => 1,
            'b'     => 2,
            'c.d'   => 3,
            'c.e'   => 4,
            'f.g.h' => 5,
        ];

        $keys = [
            'a',
            'c.d',
            'newwanted1' => 'c.e',
            'newwanted2' => 'f.g.h',
        ];

        $expectedResult = [
            'a' => 1,
            // Не изменилось.
            'd' => 3,
            // Спустилось на нижний уровень.
            'newwanted1' => 4,
            // Преобразовалось, спустилось на нижний уровень.
            'newwanted2' => 5,
            // Преобразовалось, спустилось на нижний уровень.
        ];

        $result = ArrayUtil::flatMapKeys($data, $keys);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function keysToCamel()
    {
        $result = ArrayUtil::keysToCamel(self::DATA_MIXED);

        $this->assertSame(self::DATA_CAMEL, $result);
    }

    /**
     * @test
     */
    public function keysToCamelHasNoErrorsWithEmptyArgument()
    {
        $result = ArrayUtil::keysToCamel();

        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function keysToSnake()
    {
        $result = ArrayUtil::keysToSnake(self::DATA_CAMEL);

        $this->assertSame(self::DATA_SNAKE, $result);
    }
}
