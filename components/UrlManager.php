<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\request\RequestContract;
use lb\components\response\ResponseContract;
use lb\Lb;
use ResponseKit;

class UrlManager extends BaseClass
{
    /**
     * @param $path
     * @param bool $replace
     * @param null $http_response_code
     * @param $response ResponseContract
     */
    public static function redirect($path, $replace = true, $http_response_code = null, $response = null)
    {
        if ($response) {
            $response->setHeader('Location', $path, $replace, $http_response_code);
        } else {
            ResponseKit::setHeader('Location', $path, $replace, $http_response_code);
        }
        Lb::app()->stop();
    }

    /**
     * @param $uri
     * @param array $query_params
     * @param bool $ssl
     * @param int $port
     * @param $request RequestContract
     * @return string
     */
    public static function createAbsoluteUrl($uri, $query_params = [], $ssl = false, $port = 80, $request = null)
    {
        return ($ssl ? 'https' : 'http') . '://' . ($request ? $request->getHost() : Lb::app()->getHost()) .
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
            $tmpArr[] = is_int($query_param_name) ? $query_param_value :
                implode('=', [$query_param_name, $query_param_value]);
        }
        return implode('&', $tmpArr);
    }
}
