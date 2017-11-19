<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\consts\Event;
use lb\components\error_handlers\ConsoleException;
use lb\components\error_handlers\HttpException;
use lb\components\events\AopEvent;
use lb\components\events\RequestEvent;
use lb\components\helpers\StringHelper;
use lb\components\request\RequestContract;
use lb\controllers\BaseController;
use lb\Lb;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;

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
        'seed',
        'deploy',
        'server',
        'help',
    ];

    /**
     * @param $request RequestContract
     * @return array
     */
    public static function getWebInfo($request = null)
    {
        $route_info = [
            'controller' => '',
            'action' => '',
        ];

        $request = $request ?: Lb::app();

        $request_uri = $request->getUri();
        $query_string = $request->getQueryString();
        if (Lb::app()->isPrettyUrl()) {
            if ($query_string) {
                $request_uri = str_replace('?' . $query_string, '', $request_uri);
            }
            $url_suffix = Lb::app()->getUrlSuffix();
            if ($url_suffix) {
                $request_uri = str_replace($url_suffix, '', $request_uri);
            }
            $queryParams = explode('/', trim($request_uri, '/'));
        } else {
            $queryParams = explode('/', trim($request->getParam('r'), '/'));
        }

        $isController = true;
        $isAction = false;
        foreach ($queryParams as $queryParam) {
            if ($queryParam == 'controller') {
                $isController = true;
                $isAction = false;
                continue;
            }
            if ($queryParam == 'action') {
                $isController = false;
                $isAction = true;
                continue;
            }

            if ($isController) {
                $route_info['controller'] .= $queryParam . '/';
            }
            if ($isAction) {
                $route_info['action'] .= $queryParam . '/';
            }
        }

        $route_info['controller'] = trim($route_info['controller'], '/');
        $route_info['action'] = trim($route_info['action'], '/');

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
     * @param array    $route_info
     * @param $request
     * @param $response
     * @throws HttpException
     */
    public static function hprose(Array $route_info, $request = null, $response = null)
    {
        include_once Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'hprose' . DIRECTORY_SEPARATOR . 'hprose' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Hprose.php';
        $controller_id = $route_info['controller'];
        if (in_array($controller_id, self::KERNEL_WEB_CTR)) {
            $controller_name = self::KERNEL_WEB_CTR_ROOT . ucfirst($controller_id) . 'Controller';
        } else {
            $controller_name = self::APP_WEB_CTR_ROOT . ucfirst($controller_id);
        }
        if (class_exists($controller_name)) {
            $action_name = $route_info['action'];
            if (method_exists($controller_name, $action_name)) {
                /**
 * @var BaseController $controller 
*/
                $controller = new $controller_name($controller_id, $action_name, $request, $response);
                //Trigger AOP Event
                self::triggerAopEvent($controller_id, $action_name, $request, $response);
                $server = new \Hprose\Http\Server();
                $server->addMethod($action_name, $controller);
                $server->start();
            } else {
                throw new HttpException(self::PAGE_NOT_FOUND, 404);
            }
        } else {
            throw new HttpException(self::PAGE_NOT_FOUND, 404);
        }
    }

    /**
     * @param array $routeInfo
     * @throws HttpException
     */
    public static function thrift(Array $routeInfo)
    {
        header('Content-Type', 'application/x-thrift');

        $thriftProviderConfig = Lb::app()->getThriftProviderConfig();
        if (empty($thriftProviderConfig[$routeInfo['controller']][$routeInfo['action']])) {
            throw new HttpException(self::PAGE_NOT_FOUND, 404);
        }

        $config = $thriftProviderConfig[$routeInfo['controller']][$routeInfo['action']];
        if (empty($config['service']) || empty($config['service_impl'])) {
            throw new HttpException(self::PAGE_NOT_FOUND, 404);
        }

        $serviceImpl = $config['service_impl'];
        $handler = new $serviceImpl();

        $service = $config['service'];
        $serviceArr = explode('\\', $service);
        $serviceName = ucfirst(array_pop($serviceArr));
        $processorName = '\\' . trim(implode('\\', array_merge($serviceArr, [$serviceName . 'Processor'])), '\\');
        $processor = new $processorName($handler);

        $transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
        $protocol = new TBinaryProtocol($transport, true, true);

        $transport->open();
        $processor->process($protocol, $protocol);
        $transport->close();
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
     * @param array    $route_info
     * @param $request
     * @param $response
     * @throws HttpException
     */
    public static function runWebAction(Array $route_info, $request = null, $response = null)
    {
        $controllerId = $route_info['controller'];
        $controllerIdArray = explode('/', $controllerId);
        foreach ($controllerIdArray as $k => $item) {
            $controllerIdArray[$k] = ucfirst($item);
        }
        $upperCaseControllerId = implode('\\', $controllerIdArray);
        if (in_array($controllerId, self::KERNEL_WEB_CTR)) {
            $controller_name = self::KERNEL_WEB_CTR_ROOT . $upperCaseControllerId . 'Controller';
        } else {
            $controller_name = self::APP_WEB_CTR_ROOT . $upperCaseControllerId;
        }
        if (class_exists($controller_name)) {
            $action_name = $route_info['action'];
            if (method_exists($controller_name, $action_name)) {
                /**
 * @var BaseController $controller 
*/
                $controller = new $controller_name($controllerId, $action_name, $request, $response);
                //Trigger AOP Event
                self::triggerAopEvent($controllerId, $action_name, $request, $response);
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
            if (method_exists($controller_name, $action_name)) {
                /**
 * @var BaseController $controller 
*/
                $controller = new $controller_name($controller_id, $action_name);
                //Trigger AOP Event
                self::triggerAopEvent($controller_id, $action_name);
                $controller->$action_name();
            } else {
                throw new ConsoleException(self::CONTROLLER_NOT_FOUND, 404);
            }
        } else {
            throw new ConsoleException(self::ACTION_NOT_FOUND, 404);
        }
    }

    /**
     * Trigger AOP Event
     *
     * @param $controller_id
     * @param $action_name
     * @param null          $request
     * @param null          $response
     */
    protected static function triggerAopEvent(
        $controller_id,
        $action_name,
        $request = null,
        $response = null
    ) {
    
        $context = [
            'controller_id' => $controller_id,
            'action_id' => $action_name,
        ];
        if ($request) {
            $context['request'] = $request;
        }
        if ($response) {
            $context['response'] = $response;
        }
        Lb::app()->trigger(
            Event::AOP_EVENT . '_' . implode('@', [$controller_id, $action_name]),
            new AopEvent($context)
        );
        Lb::app()->trigger(
            Event::REQUEST_EVENT,
            new RequestEvent($context)
        );
    }
}
