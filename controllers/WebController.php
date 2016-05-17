<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午9:21
 * Lb framework web controller file
 */

namespace lb\controllers;

use lb\components\error_handlers\HttpException;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\Lb;
use lb\components\Render;

class WebController extends BaseController
{
    protected $layout = 'default';
    protected $public_params = [];

    protected function beforeRenderJson()
    {

    }

    protected function beforeRenderXML()
    {

    }

    protected function beforeRenderJsAlert()
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

    protected function renderJson($array, $is_string = true, $return = false)
    {
        $this->beforeRenderJson();
        $json = JsonHelper::encode($array);
        if ($return) {
            return $json;
        } else {
            if (!$is_string) {
                header('Content-type:application/json');
            }
            echo $json;
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
            echo $xml;
        }
    }

    protected function renderJsAlert($content, $return = false)
    {
        $this->beforeRenderJsAlert();
        if (is_array($content)) {
            if (!ArrayHelper::is_multi_array($content)) {
                $alert = implode(PHP_EOL, $content);
            } else {
                $alert = print_r($content, true);
            }
        } else {
            $alert = $content;
        }
        $js_alert_tpl = '<script>alert(\'%s\')</script>';
        $js_alert_code = sprintf($js_alert_tpl, $alert);
        if ($return) {
            return $js_alert_code;
        } else {
            echo $js_alert_code;
        }
    }

    protected function render($template_name, $params = [], $return = false)
    {
        $this->beforeRender();
        $params += ['controller' => $this];
        $params += $this->public_params;
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            return Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
        } else {
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
        }
    }

    protected function renderPartial($template_name, $params = [], $return = false)
    {
        $this->beforeRenderPartial();
        $params += ['controller' => $this];
        $params += $this->public_params;
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            return Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
        } else {
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
        }
    }

    protected function assign($param_name, $param_value)
    {
        $this->public_params[$param_name] = $param_value;
    }

    protected function get($param_name) {
        if (array_key_exists($param_name, $this->public_params)) {
            return $this->public_params[$param_name];
        }
        return '';
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
                    $forward_url = Lb::app()->createRelativeUrl("/{$this->controller_id}/action/{$action_id}", $params);
                } else {
                    $forward_url = Lb::app()->createRelativeUrl("/index.php?{$this->controller_id}&action={$action_id}", $params);
                }
                Lb::app()->redirect($forward_url, $replace, $http_response_code);
            } else {
                throw new HttpException('Path is empty.', 500);
            }
        }
    }

    protected function isPost()
    {
        return strtolower(Lb::app()->getRequestMethod()) == 'post';
    }

    protected function isAjax()
    {
        return Lb::app()->isAjax();
    }

    public function error($err_msg, $tpl_name)
    {
        $viewPath = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . "{$tpl_name}.php";
        if (file_exists($viewPath)) {
            $this->render($tpl_name, [
                'title' => 'Exception',
                'err_msg' => $err_msg,
            ]);
        } else {
            echo $err_msg;
        }
    }
}
