<?php

namespace lb\components;

use lb\BaseClass;
use lb\Lb;

class UrlManager extends BaseClass
{
    /**
     * @param $path
     * @param bool $replace
     * @param null $http_response_code
     */
    public static function redirect($path, $replace = true, $http_response_code = null)
    {
        header("Location: $path", $replace, $http_response_code);
        Lb::app()->stop();
    }

    /**
     * @param $uri
     * @param array $query_params
     * @param bool $ssl
     * @param int $port
     * @return string
     */
    public static function createAbsoluteUrl($uri, $query_params = [], $ssl = false, $port = 80)
    {
        return ($ssl ? 'https' : 'http') . '://' . Lb::app()->getHost() .
            ($port == 80 ? '' : ':' . $port) . static::createRelativeUrl($uri, $query_params);
    }

    /**
     * @param $uri
     * @param array $query_params
     * @return string
     */
    public static function createRelativeUrl($uri, $query_params = [])
    {
        if ($query_params && ($query = static::build_query($query_params))) {
            $uri .= ((strpos($uri, '?') !== false ? '&' : '?') . $query);
        }

        return $uri;
    }

    /**
     * @param $query_params
     * @return string
     */
    public static function build_query($query_params)
    {
        $tmpArr = [];
        foreach ($query_params as $query_param_name => $query_param_value) {
            if (is_int($query_param_name)) {
                $tmpArr[] = $query_param_value;
            } else {
                $tmpArr[] = implode('=', [$query_param_name, $query_param_value]);
            }
        }
        return implode('&', $tmpArr);
    }
}
