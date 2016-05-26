<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/5/26
 * Time: 16:17
 * Lb framework maths helper component file
 */

namespace lb\components\helpers;

use lb\BaseClass;

class MathsHelper extends BaseClass
{
    public static function randomFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
