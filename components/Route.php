<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午1:26
 * Lb framework route component file
 */

namespace lb\components;

use lb\Lb;

class Route
{
    public static function getInfo()
    {
        $route_info = [
            'controller' => '',
            'action' => '',
            'id' => '',
        ];
        $request_uri = Lb::app()->getUri();
        if (Lb::app()->isPrettyUrl()) {
            $query_params = explode('/', trim($request_uri, '/'));
            $route_info['controller'] = array_shift($query_params);
            foreach ($query_params as $key => $query_param) {
                if (array_key_exists($query_param, $route_info) && $query_param != 'controller') {
                    $route_info[$query_param] = $query_params[$key + 1];
                }
            }
        } else {
            if (strpos($request_uri, '?') !== false) {
                $query_params = explode('&', Lb::app()->getQueryString());
                if ($query_params) {
                    $route_info['controller'] = array_shift($query_params);
                    foreach ($query_params as $query_param) {
                        if (strpos($query_param, '=') !== false) {
                            list($query_param_name, $query_param_value) = explode('=', $query_param);
                            if (array_key_exists($query_param_name, $route_info) && $query_param_name != 'controller') {
                                $route_info[$query_param_name] = $query_param_value;
                            }
                        }
                    }
                }
            }
        }
        return $route_info;
    }

    public static function redirect($route_info)
    {
        $controller_id = $route_info['controller'];
        $controller_name = 'app\controllers\\' . ucfirst($controller_id);
        $action_name = $route_info['action'];
        $controller = new $controller_name();
        $controller->controller_id = $controller_id;
        if (method_exists($controller, $action_name)) {
            $method = new \ReflectionMethod($controller_name, $action_name);
            $params = $method->getParameters();
            $param_values = [];
            foreach ($params as $param) {
                $param_name = $param->getName();
                if (array_key_exists($param_name, $_REQUEST)) {
                    $param_values[] = $_REQUEST[$param_name];
                } else {
                    try {
                        $param_values[] = $param->getDefaultValue();
                    } catch (\Exception $e) {
                        $param_values[] = null;
                    }
                }
            }
            if ($param_values) {
                $method->invokeArgs($controller, $param_values);
            } else {
                $controller->$action_name();
            }
        }
    }
}
