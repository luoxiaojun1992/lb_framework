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
        $oneDimensionalArrayExpectedString = <<<oneDimensionalArrayExpectedString
Array
(
    [name] => a
    [age] => 23
)
oneDimensionalArrayExpectedString;
        $twoDimensionalArrayExpectedString = <<<twoDimensionalArrayExpectedString
Array
(
    [0] => Array
        (
            [name] => a
            [age] => 23
        )

    [1] => Array
        (
            [name] => b
            [age] => 24
        )

)
twoDimensionalArrayExpectedString;

        $oneDimensionalArrayString = ArrayHelper::toString($oneDimensionalArray);
        $twoDimensionalArrayString = ArrayHelper::toString($twoDimensionalArray);
        $this->assertEquals($oneDimensionalArrayString, $oneDimensionalArrayExpectedString);
        $this->assertEquals($twoDimensionalArrayString, $twoDimensionalArrayExpectedString);
    }
}
