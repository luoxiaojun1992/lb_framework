<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/15
 * Time: 11:26
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\ArrayHelper;

class ArrayHelperTest extends \PHPUnit_Framework_TestCase
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
        $notArrayExpectedString = print_r($notArray, true);

        $oneDimensionalArrayString = ArrayHelper::toString($oneDimensionalArray);
        $twoDimensionalArrayString = ArrayHelper::toString($twoDimensionalArray);
        $threeDimensionalArrayString = ArrayHelper::toString($threeDimensionalArray);
        $notArrayString = ArrayHelper::toString($notArray);
        $this->assertEquals($oneDimensionalArrayString, $oneDimensionalArrayExpectedString);
        $this->assertEquals($twoDimensionalArrayString, $twoDimensionalArrayExpectedString);
        $this->assertEquals($threeDimensionalArrayString, $threeDimensionalArrayExpectedString);
        $this->assertEquals($notArrayString, $notArrayExpectedString);
    }
}
