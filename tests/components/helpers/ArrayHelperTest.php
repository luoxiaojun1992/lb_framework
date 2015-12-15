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
        $oneDimensionalArrayExpectedString = print_r($oneDimensionalArray, true);
        $twoDimensionalArrayExpectedString = print_r($twoDimensionalArray, true);
        $threeDimensionalArrayExpectedString = print_r($threeDimensionalArray, true);

        $oneDimensionalArrayString = ArrayHelper::toString($oneDimensionalArray);
        $twoDimensionalArrayString = ArrayHelper::toString($twoDimensionalArray);
        $threeDimensionalArrayString = ArrayHelper::toString($threeDimensionalArray);
        $this->assertEquals($oneDimensionalArrayString, $oneDimensionalArrayExpectedString);
        $this->assertEquals($twoDimensionalArrayString, $twoDimensionalArrayExpectedString);
        $this->assertEquals($threeDimensionalArrayString, $threeDimensionalArrayExpectedString);
    }
}
