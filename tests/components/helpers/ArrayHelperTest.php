<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\ArrayHelper;
use lb\tests\BaseTestCase;

class ArrayHelperTest extends BaseTestCase
{
    public function testArrayDepth()
    {
        $oneDimensionalArray = ['name' => 'a', 'age' => 23];
        $twoDimensionalArray = [
            ['name' => 'a', 'age' => 23],
            ['name' => 'b', 'age' => 24],
        ];
        $threeDimensionalArray = [
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $notArray = 'name a age 23';

        $oneDimensionalArrayDepth = ArrayHelper::array_depth($oneDimensionalArray);
        $twoDimensionalArrayDepth = ArrayHelper::array_depth($twoDimensionalArray);
        $threeDimensionalArrayDepth = ArrayHelper::array_depth($threeDimensionalArray);
        $notArrayDepth = ArrayHelper::array_depth($notArray);
        $this->assertEquals(1, $oneDimensionalArrayDepth);
        $this->assertEquals(2, $twoDimensionalArrayDepth);
        $this->assertEquals(3, $threeDimensionalArrayDepth);
        $this->assertEquals(0, $notArrayDepth);
    }

    public function testIsMultiArray()
    {
        $oneDimensionalArray = ['name' => 'a', 'age' => 23];
        $twoDimensionalArray = [
            ['name' => 'a', 'age' => 23],
            ['name' => 'b', 'age' => 24],
        ];
        $threeDimensionalArray = [
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $notArray = 'name a age 23';

        $this->assertFalse(ArrayHelper::is_multi_array($oneDimensionalArray));
        $this->assertTrue(ArrayHelper::is_multi_array($twoDimensionalArray));
        $this->assertTrue(ArrayHelper::is_multi_array($threeDimensionalArray));
        $this->assertFalse(ArrayHelper::is_multi_array($notArray));
    }

    public function testToString()
    {
        $oneDimensionalArray = ['name' => 'a', 'age' => 23];
        $twoDimensionalArray = [
            ['name' => 'a', 'age' => 23],
            ['name' => 'b', 'age' => 24],
        ];
        $threeDimensionalArray = [
            [
                ['name' => 'a', 'age' => 23],
                ['name' => 'b', 'age' => 24],
            ],
        ];
        $notArray = 'name a age 23';
        $oneDimensionalArrayExpectedString = print_r($oneDimensionalArray, true);
        $twoDimensionalArrayExpectedString = print_r($twoDimensionalArray, true);
        $threeDimensionalArrayExpectedString = print_r($threeDimensionalArray, true);

        $oneDimensionalArrayString = ArrayHelper::toString($oneDimensionalArray);
        $twoDimensionalArrayString = ArrayHelper::toString($twoDimensionalArray);
        $threeDimensionalArrayString = ArrayHelper::toString($threeDimensionalArray);
        $notArrayString = ArrayHelper::toString($notArray);
        $this->assertEquals($oneDimensionalArrayExpectedString, $oneDimensionalArrayString);
        $this->assertEquals($twoDimensionalArrayExpectedString, $twoDimensionalArrayString);
        $this->assertEquals($threeDimensionalArrayExpectedString, $threeDimensionalArrayString);
        $this->assertEquals($notArray, $notArrayString);
    }

    public function testListData()
    {
        $oneDimensionalArray = ['name' => 'a', 'age' => 23];
        $twoDimensionalArray = [
            ['name' => 'a', 'age' => 23],
            ['name' => 'b', 'age' => 24],
        ];

        $expectedOneDimensionalListData = [];
        $expectedTwoDimensionalListData = [
            'a' => '23',
            'b' => '24',
        ];

        $actualOneDimensionalListData = ArrayHelper::listData($oneDimensionalArray, 'name', 'age');
        $actualTwoDimensionalListData = ArrayHelper::listData($twoDimensionalArray, 'name', 'age');

        $this->assertEquals($expectedOneDimensionalListData, $actualOneDimensionalListData);
        $this->assertEquals($expectedTwoDimensionalListData, $actualTwoDimensionalListData);
    }
}
