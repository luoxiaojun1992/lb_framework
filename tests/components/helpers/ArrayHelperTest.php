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
        var_dump(ArrayHelper::toString($oneDimensionalArray));
        var_dump(ArrayHelper::toString($twoDimensionalArray));
    }
}
