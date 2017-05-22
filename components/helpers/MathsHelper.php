<?php

namespace lb\components\helpers;

use lb\BaseClass;

class MathsHelper extends BaseClass
{
    public static function randomFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    public static function times($base, $times, $timesTwo = false)
    {
        if ($timesTwo || $times % 2 == 0) {
            return $base << ($times / 2);
        }

        return $base * $times;
    }

    public static function divide($base, $div, $divTwo = false)
    {
        if ($divTwo || $div % 2 == 0) {
            return $base >> ($div / 2);
        }

        return $base / $div;
    }

    public static function getTargetByProbility($probilities)
    {
        asort($probilities);
        $randomFloat = self::randomFloat(0, max($probilities));
        foreach ($probilities as $k => $probility) {
            if ($randomFloat <= $probility) {
                return $k;
            }
        }

        return false;
    }

    public static function is2pow($number)
    {
        return !($number & ($number - 1));
    }
}
