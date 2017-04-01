<?php

namespace lb\controllers;

use lb\BaseClass;
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
        foreach ($this->middleware as $middleware) {
            $action = !empty($middleware['action']) ? $middleware['action'] : 'runAction';
            $params = !empty($middleware['params']) ? $middleware['params'] : [];
            $successCallback = !empty($middleware['successCallback']) ? $middleware['successCallback'] : null;
            $failureCallback = !empty($middleware['failureCallback']) ? $middleware['failureCallback'] : null;
            $middlewareClass = $middleware['class'];
            if (!call_user_func_array([new $middlewareClass, $action], [
                'params' => $params,
                'successCallback' => $successCallback,
                'failureCallback' => $failureCallback,
            ])) {
                break;
            }
        }
    }

    protected function beforeAction()
    {
        $this->runMiddleware();
    }
}
