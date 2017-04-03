<?php

namespace lb\controllers;

use lb\BaseClass;
use lb\components\middleware\MiddlewareInterface;
use lb\components\Request;
use lb\components\Response;

abstract class BaseController extends BaseClass
{
    protected $controller_id = '';

    /** @var  Request */
    protected $request;

    /** @var  Response */
    protected $response;

    protected $middleware = [];

    public function __construct($controllerId, $request = null, $response = null)
    {
        $this->setControllerId($controllerId)
            ->setRequest($request)
            ->setResponse($response)
            ->beforeAction();
    }

    public function __clone()
    {
        //
    }

    /**
     * Run middlewares
     */
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

    /**
     * Before action filter
     */
    protected function beforeAction()
    {
        $this->runMiddleware();
    }

    /**
     * Set controller id
     *
     * @param $controllerId
     * @return $this
     */
    public function setControllerId($controllerId)
    {
        $this->controller_id = $controllerId;
        return $this;
    }

    /**
     * Set request
     *
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set response
     *
     * @param $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }
}
