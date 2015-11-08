<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/8
 * Time: 上午9:59
 * Lb framework url manager component file
 */

namespace lb\components;

use lb\Lb;

class UrlManager
{
    public static function redirect($path, $replace = true, $http_response_code = null)
    {
        header("Location: $path", $replace, $http_response_code);
    }

    public static function createAbsoluteUrl($uri, $query_params = [])
    {
        if (strpos($uri, '?') !== false) {
            $tmpArr = [];
            foreach ($query_params as $query_param_name => $query_param_value) {
                if (is_int($query_param_name)) {
                    $tmpArr[] = $query_param_value;
                } else {
                    $tmpArr[] = implode('=', [$query_param_name, $query_param_value]);
                }
            }
            if ($tmpArr) {
                $uri .= ('&' . implode('&', $tmpArr));
            }
        } else {
            $tmpArr = [];
            foreach ($query_params as $query_param_name => $query_param_value) {
                if (is_int($query_param_name)) {
                    $tmpArr[] = $query_param_value;
                } else {
                    $tmpArr[] = implode('=', [$query_param_name, $query_param_value]);
                }
            }
            if ($tmpArr) {
                $uri .= ('?' . implode('&', $tmpArr));
            }
        }
        return Lb::app()->getHost() . $uri;
    }
}
