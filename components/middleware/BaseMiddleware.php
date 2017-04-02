<?php

namespace lb\components\middleware;

use lb\BaseClass;

abstract class BaseMiddleware extends BaseClass
{
    public $serial;
    public $middlewares;

    abstract public function runAction($params, $successCallback, $failureCallback);

    public function setSerial($serial)
    {
        $this->serial = $serial;
    }

    public function getSerial()
    {
        return $this->serial;
    }

    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function runNextMiddleware()
    {
        $nextSerial = $this->getSerial() + 1;
        $middlewares = $this->getMiddlewares();
        if (isset($middlewares[$nextSerial])) {
            $middlewareConfig = $middlewares[$nextSerial];
            $middlewareClass = $middlewareConfig['class'];
            /** @var MiddlewareInterface $middleware */
            $middleware = new $middlewareClass;
            $middleware->setSerial($nextSerial);
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
}
