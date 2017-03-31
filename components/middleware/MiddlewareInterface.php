<?php

namespace lb\components\middleware;

interface MiddlewareInterface
{
    public function returnAction();

    public function exceptionAction();
}
