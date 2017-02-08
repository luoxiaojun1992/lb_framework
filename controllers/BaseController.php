<?php

namespace lb\controllers;

use lb\BaseClass;

abstract class BaseController extends BaseClass
{
    public $controller_id = '';

    public function __construct()
    {
        $this->beforeAction();
    }

    public function __clone()
    {
        //
    }

    abstract protected function beforeAction();
}
