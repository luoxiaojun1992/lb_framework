<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\error_handlers\ConsoleException;
use lb\components\error_handlers\HttpException;
use lb\Lb;

class Route extends BaseClass
{
    const KERNEL_WEB_CTR_ROOT = 'lb\controllers\web\\';
    const KERNEL_CONSOLE_CTR_ROOT = 'lb\controllers\console\\';
    const APP_WEB_CTR_ROOT = 'app\controllers\web\\';
    const APP_CONSOLE_CTR_ROOT = 'app\controllers\console\\';
    const KERNEL_WEB_CTR = [
        'web',
    ];
    const KERNEL_WEB_ACTIONS = [
        'error',
        'api',
    ];
    const KERNEL_CONSOLE_CTR = [
        'system',
        'tink',
        'migrate',
        'queue',
        'model',
        'swoole',
    ];

    /**
     * @return array
     */
    public static function getWebInfo()
    {
        $route_info = [
            'controller' => '',
            'action' => '',
            'id' => '',
        ];
        $request_uri = Lb::app()->getUri();
        $query_string = Lb::app()->getQueryString();
        if (Lb::app()->isPrettyUrl()) {
            if ($query_string) {
                $request_uri = str_replace('?' . $query_string, '', $request_uri);
            }
            $url_suffix = Lb::app()->getUrlSuffix();
            if ($url_suffix) {
                $request_uri = str_replace($url_suffix, '', $request_uri);
            }
            $query_params = explode('/', trim($request_uri, '/'));
            $route_info['controller'] = array_shift($query_params);
            foreach ($query_params as $key => $query_param) {
                if (array_key_exists($query_param, $route_info) && $query_param != 'controller') {
                    $route_info[$query_param] = $query_params[$key + 1];
                }
            }
        } else {
            if (strpos($request_uri, '?') !== false) {
                $query_params = explode('&', $query_string);
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

    /**
     * @return array
     */
    public static function getConsoleInfo()
    {
        $route_info = [
            'controller' => '',
            'action' => 'index',
        ];
        if (isset($_SERVER['argc']) && isset($_SERVER['argv']) && $_SERVER['argc'] > 1) {
            $controller_action_info = $_SERVER['argv'][1];
            if (strpos($controller_action_info, '/') > 0) {
                list($route_info['controller'], $route_info['action']) = explode('/', $controller_action_info);
                $route_info['action'] = $route_info['action'] ? : 'index';
            } else {
                $route_info['controller'] = $controller_action_info;
            }
        }
        return $route_info;
    }

    /**
     * @param array $route_info
     * @throws HttpException
     */
    public static function rpc(Array $route_info)
    {
        require_once(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'hprose' . DIRECTORY_SEPARATOR . 'hprose' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Hprose.php');
        $controller_id = $route_info['controller'];
        if (in_array($controller_id, self::KERNEL_WEB_CTR)) {
            $controller_name = self::KERNEL_WEB_CTR_ROOT . ucfirst($controller_id) . 'Controller';
        } else {
            $controller_name = self::APP_WEB_CTR_ROOT . ucfirst($controller_id);
        }
        if (class_exists($controller_name)) {
            $action_name = $route_info['action'];
            if (method_exists($controller_name, $action_name)) {
                $server = new \Hprose\Http\Server();
                $server->addMethod($action_name, new $controller_name());
                $server->start();
            } else {
                throw new HttpException(self::PAGE_NOT_FOUND, 404);
            }
        } else {
            throw new HttpException(self::PAGE_NOT_FOUND, 404);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return array
     */
    protected static function matchActionParams(\ReflectionMethod $method)
    {
        $param_values = [];
        foreach ($method->getParameters() as $param) {
            if ($class = $param->getClass()) {
                $param_values[] = Lb::app()->getDIContainer()->get($class->getName());
            } else {
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
        }

        return $param_values;
    }

    /**
     * @param array $route_info
     * @throws HttpException
     */
    public static function runWebAction(Array $route_info)
    {
        $controller_id = $route_info['controller'];
        if (in_array($controller_id, self::KERNEL_WEB_CTR)) {
            $controller_name = self::KERNEL_WEB_CTR_ROOT . ucfirst($controller_id) . 'Controller';
        } else {
            $controller_name = self::APP_WEB_CTR_ROOT . ucfirst($controller_id);
        }
        if (class_exists($controller_name)) {
            $action_name = $route_info['action'];
            $controller = new $controller_name();
            $controller->controller_id = $controller_id;
            if (method_exists($controller, $action_name)) {
                $method = new \ReflectionMethod($controller, $action_name);
                $method->invokeArgs($controller, self::matchActionParams($method));
            } else {
                throw new HttpException(self::PAGE_NOT_FOUND, 404);
            }
        } else {
            throw new HttpException(self::PAGE_NOT_FOUND, 404);
        }
    }

    /**
     * @param array $route_info
     * @throws ConsoleException
     */
    public static function runConsoleAction(Array $route_info)
    {
        $controller_id = $route_info['controller'];
        if (in_array($controller_id, self::KERNEL_CONSOLE_CTR)) {
            $controller_name = self::KERNEL_CONSOLE_CTR_ROOT . ucfirst($controller_id) . 'Controller';
        } else {
            $controller_name = self::APP_CONSOLE_CTR_ROOT . ucfirst($controller_id);
        }
        if (class_exists($controller_name)) {
            $action_name = $route_info['action'];
            $controller = new $controller_name();
            $controller->controller_id = $controller_id;
            if (method_exists($controller, $action_name)) {
                $controller->$action_name();
            } else {
                throw new ConsoleException(self::CONTROLLER_NOT_FOUND, 404);
            }
        } else {
            throw new ConsoleException(self::ACTION_NOT_FOUND, 404);
        }
    }
}
