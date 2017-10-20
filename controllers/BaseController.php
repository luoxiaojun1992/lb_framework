<?php

namespace lb\controllers;

use lb\BaseClass;
use lb\components\request\RequestContract;
use lb\components\middleware\MiddlewareInterface;
use lb\components\response\ResponseContract;

abstract class BaseController extends BaseClass
{
    public $controller_id = '';
    public $action_id = '';

    /** @var  RequestContract */
    public $request;

    /** @var  ResponseContract */
    public $response;

    protected $middleware = [];

    public function __construct($controllerId, $actionId, RequestContract $request = null, ResponseContract $response = null)
    {
        $this->setControllerId($controllerId)
            ->setActionId($actionId)
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
     * Set action id
     *
     * @param $actionId
     * @return $this
     */
    public function setActionId($actionId)
    {
        $this->action_id = $actionId;
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

    /**
     * Set Flash Message
     *
     * @param $flashKey
     * @param $content
     */
    public function setFlash($flashKey, $content)
    {
        $this->response->setSession($flashKey, $content);
    }

    /**
     * Get Flash Message
     *
     * @param $flashKey
     * @param $once
     * @return mixed
     */
    public function getFlash($flashKey, $once = true)
    {
        $content = $this->request->getSession($flashKey);
        if ($content && $once) {
            $this->response->delSession($flashKey);
        }

        return $content;
    }
}
