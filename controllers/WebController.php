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

    protected function beforeRender()
    {

    }

    protected function beforeRedirect()
    {

    }

    protected function render($template_name, $params, $return = false)
    {
        $this->beforeRender();
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            return Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
        } else {
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
        }
    }

    protected function redirect($path, $replace = true, $http_response_code = null)
    {
        $this->beforeRedirect();
        if (!is_array($path)) {
            Lb::app()->redirect($path, $replace, $http_response_code);
        } else {
            if ($path) {
                $action_id = array_shift($path);
                $params = $path;
                $forward_url = Lb::app()->createAbsoluteUrl("/index.php?{$this->controller_id}&action={$action_id}", $params);
                Lb::app()->redirect($forward_url, $replace, $http_response_code);
            }
            Lb::app()->stop();
        }
    }
}
