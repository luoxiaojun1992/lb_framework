<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\Lb;

/**
 * Class ArrayHelper
 * @package lb\components\helpers
 */
class ArrayHelper extends BaseClass
{
    /**
     * @param $array
     * @return int
     */
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

    /**
     * @param $array
     * @return bool
     */
    public static function is_multi_array($array)
    {
        $result = false;
        if (is_array($array)) {
            $result = static::array_depth($array) > 1;
        }
        return $result;
    }

    /**
     * @param $array
     * @return mixed
     */
    public static function toString($array)
    {
        if (is_array($array)) {
            $string = print_r($array, true);
        } else {
            $string = $array;
        }
        return $string;
    }

    /**
     * @param $array
     * @param $key
     * @param $value
     * @return array
     */
    public static function listData($array, $key, $value)
    {
        $listData = [];
        if (static::array_depth($array) == 2) {
            foreach ($array as $child_array) {
                $listData[$child_array[$key]] = $child_array[$value];
            }
        }
        return $listData;
    }

    /**
     * @param $array
     * @param string $func
     */
    public static function debug($array, $func = 'var_dump')
    {
        echo '<pre>';
        $func($array);
        echo '</pre>';
        Lb::app()->stop();
    }
}
