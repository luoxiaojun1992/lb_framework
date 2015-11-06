<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午9:21
 * Lb framework web controller file
 */

namespace lb\controllers;

use lb\components\Render;

class WebController extends BaseController
{
    protected $layout = 'default';

    protected function render($template_name, $params)
    {
        Render::output($template_name, $params, $this->layout);
    }
}
