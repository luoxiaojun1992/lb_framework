<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/8
 * Time: 上午9:59
 * Lb framework url manager component file
 */

namespace lb\components;

class UrlManager
{
    public static function redirect($path, $replace = true, $http_response_code = null)
    {
        header("Location: $path", $replace, $http_response_code);
    }
}
