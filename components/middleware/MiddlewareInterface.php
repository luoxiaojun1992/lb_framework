<?php

namespace lb\components\middleware;

interface MiddlewareInterface
{
    public function runAction($params, $successCallback, $failureCallback);

    public function setSerial($serial);

    public function getSerial();

    public function setMiddlewares($middlewares);

    public function getMiddlewares();

    public function runNextMiddleware();
}
