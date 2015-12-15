<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 14:26
 * Lb framework array helper component file
 */

namespace lb\components\helpers;

class ArrayHelper
{
    public static function array_depth($array)
    {
        if (is_array($array)) {
            $max_depth = 1;

            foreach ($array as $value) {
                if (is_array($value)) {
                    $depth = static::array_depth($value) + 1;

                    if ($depth > $max_depth) {
                        $max_depth = $depth;
                    }
                }
            }
            return $max_depth;
        }
        return 0;
    }

    public static function is_multi_array($array)
    {
        $result = false;
        if (is_array($array)) {
            $result = static::array_depth($array) > 1;
        }
        return $result;
    }

    public static function toString($array)
    {
        if (is_array($array)) {
            $string = print_r($array, true);
        } else {
            $string = $array;
        }
        return $string;
    }
}
