<?php

namespace lb\components\middleware;

interface MiddlewareInterface
{
    public function runAction($params, $successCallback, $failureCallback);
}
