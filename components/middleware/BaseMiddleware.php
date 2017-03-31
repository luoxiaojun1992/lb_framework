<?php

namespace lb\components\middleware;

use lb\BaseClass;

abstract class BaseMiddleware extends BaseClass
{
    public function runAction($params, $successCallback, $failureCallback)
    {
        return true;
    }
}
