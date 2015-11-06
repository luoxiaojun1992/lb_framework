<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午1:26
 * Lb framework route file
 */

namespace lb;

class Route
{
    public static function getInfo()
    {
        $route_info = [
            'controller' => '',
            'action' => '',
            'id' => '',
        ];
        $request_uri = $_SERVER['REQUEST_URI'];
        if (strpos($request_uri, '?') !== false) {
            $query_params = explode('&', $_SERVER['QUERY_STRING']);
            if ($query_params) {
                $route_info['controller'] = array_shift($query_params);
                foreach ($query_params as $query_param => $query_param_value) {
                    if (array_key_exists($query_param, $route_info)) {
                        $route_info[$query_param] = $query_param_value;
                    }
                }
            }
        }
        return $route_info;
    }
}
