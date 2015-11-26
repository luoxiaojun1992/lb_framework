<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午9:21
 * Lb framework web controller file
 */

namespace lb\controllers;

use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\Lb;
use lb\components\Render;

class WebController extends BaseController
{
    protected $layout = 'default';

    protected function beforeRenderJson()
    {

    }

    protected function beforeRenderXML()
    {

    }

    protected function beforeRender()
    {

    }

    protected function beforeRenderPartial()
    {

    }

    protected function beforeRedirect()
    {

    }

    protected function renderJson($array, $return = false)
    {
        $this->beforeRenderJson();
        $json = JsonHelper::encode($array);
        if ($return) {
            return $json;
        } else {
            Lb::app()->stop($json);
        }
    }

    protected function renderXML($array, $return = false)
    {
        $this->beforeRenderXML();
        $xml = XMLHelper::encode($array);
        if ($return) {
            return $xml;
        } else {
            Header('Content-type:application/xml');
            Lb::app()->stop($xml);
        }
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

    public function renderPartial($template_name, $params, $return = false)
    {
        $this->beforeRenderPartial();
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            return Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
        } else {
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
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
                if (Lb::app()->isPrettyUrl()) {
                    $forward_url = Lb::app()->createAbsoluteUrl("/{$this->controller_id}/action/{$action_id}", $params);
                } else {
                    $forward_url = Lb::app()->createAbsoluteUrl("/index.php?{$this->controller_id}&action={$action_id}", $params);
                }
                Lb::app()->redirect($forward_url, $replace, $http_response_code);
            }
            Lb::app()->stop();
        }
    }

    protected function isPost()
    {
        return strtolower(Lb::app()->getRequestMethod()) == 'post';
    }
}
