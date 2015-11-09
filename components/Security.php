<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/8
 * Time: 下午10:53
 * Lb framework security component file
 */

namespace lb\components;

class Security
{
    public static function inputFilter()
    {
        foreach ($_REQUEST as $request_name => $request_value) {
            $_REQUEST[$request_name] = self::getFilteredInput($request_value);
        }
    }

    protected static function getFilteredInput($input_value)
    {
        $input_value = addslashes($input_value);
        return $input_value;
    }

    public static function generateCsrfToken()
    {
        return md5(uniqid(rand(), true));
    }
}