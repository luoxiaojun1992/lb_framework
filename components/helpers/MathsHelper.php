<?php

namespace lb\components\helpers;

use lb\BaseClass;

class MathsHelper extends BaseClass
{
    public static function randomFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
