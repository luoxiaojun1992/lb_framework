<?php

namespace lb\components\middleware;

use lb\BaseClass;

abstract class BaseMiddleware extends BaseClass
{
    public function returnAction()
    {
        return true;
    }

    public function exceptionAction()
    {
        //
    }
}
