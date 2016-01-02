<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午3:12
 * Lb framework base controller file
 */

namespace lb\controllers;

use lb\BaseClass;

class BaseController extends BaseClass
{
    public $controller_id = '';

    public function __construct()
    {
        $this->beforeAction();
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    protected function beforeAction()
    {

    }
}
