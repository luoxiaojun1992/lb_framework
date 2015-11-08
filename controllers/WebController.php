<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午9:21
 * Lb framework web controller file
 */

namespace lb\controllers;

use lb\Lb;
use lb\components\Render;

class WebController extends BaseController
{
    protected $layout = 'default';

    protected function render($template_name, $params, $return = false)
    {
        if ($return) {
            return Render::output($template_name, $params, $this->layout, $return);
        } else {
            Render::output($template_name, $params, $this->layout, $return);
        }
    }

    protected function redirect($path, $replace = true, $http_response_code = null)
    {
        if (!is_array($path)) {
            Lb::app()->redirect($path, $replace, $http_response_code);
        } else {

        }
    }
}
