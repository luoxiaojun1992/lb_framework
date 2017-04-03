<?php

namespace lb\controllers;

use lb\BaseClass;
use lb\components\middleware\MiddlewareInterface;
use lb\Lb;

abstract class BaseController extends BaseClass
{
    public $controller_id = '';
    protected $middleware = [];

    public function __construct()
    {
        $this->beforeAction();
    }

    public function __clone()
    {
        //
    }

    protected function runMiddleware()
    {
        $middlewareSerial = 0;
        $middlewares = array_values($this->middleware);
        if (isset($middlewares[$middlewareSerial])) {
            $middlewareConfig = $middlewares[$middlewareSerial];
            $middlewareClass = $middlewareConfig['class'];
            /** @var MiddlewareInterface $middleware */
            $middleware = new $middlewareClass;
            $middleware->setSerial($middlewareSerial);
            $middleware->setMiddlewares($middlewares);
            $action = !empty($middlewareConfig['action']) ? $middlewareConfig['action'] : 'runAction';
            $params = !empty($middlewareConfig['params']) ? $middlewareConfig['params'] : [];
            $successCallback = !empty($middlewareConfig['successCallback']) ?
                $middlewareConfig['successCallback'] : null;
            $failureCallback = !empty($middlewareConfig['failureCallback']) ?
                $middlewareConfig['failureCallback'] : null;
            call_user_func_array([$middleware, $action], [
                'params' => $params,
                'successCallback' => $successCallback,
                'failureCallback' => $failureCallback,
            ]);
        }
    }

    protected function beforeAction()
    {
        $this->runMiddleware();
    }
}
