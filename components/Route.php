<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午1:26
 * Lb framework route component file
 */

namespace lb\components;

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
                foreach ($query_params as $query_param) {
                    if (strpos($query_param, '=') !== false) {
                        list($query_param_name, $query_param_value) = explode('=', $query_param);
                        if (array_key_exists($query_param_name, $route_info)) {
                            $route_info[$query_param_name] = $query_param_value;
                        }
                    }
                }
            }
        }
        return $route_info;
    }

    public static function redirect($route_info)
    {
        $controller_name = 'app\controllers\\' . ucfirst($route_info['controller']);
        $action_name = $route_info['action'];
        $controller = new $controller_name();
        if (method_exists($controller, $action_name)) {
            $controller->$action_name();
        }
    }
}
