<?php

namespace lb\controllers\web;

use DebugBar\StandardDebugBar;
use lb\components\error_handlers\HttpException;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\controllers\BaseController;
use lb\Lb;
use lb\components\Render;

class WebController extends BaseController
{
    protected $layout = 'default';
    protected $public_params = [];

    protected function beforeAction()
    {
        parent::beforeAction();
    }

    protected function beforeRenderJson()
    {
        //
    }

    protected function beforeRenderXML()
    {
        //
    }

    protected function beforeRenderJsAlert()
    {
        //
    }

    protected function beforeRender()
    {
        //
    }

    protected function beforeRenderPartial()
    {
        //
    }

    protected function beforeRedirect()
    {
        //
    }

    protected function renderJson($array, $is_string = true, $return = false, $status_code = 200)
    {
        $this->beforeRenderJson();
        $this->response->httpCode($status_code);
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

    protected function renderXML($array, $return = false, $status_code = 200)
    {
        $this->beforeRenderXML();
        $this->response->httpCode($status_code);
        $xml = XMLHelper::encode($array);
        if ($return) {
            return $xml;
        } else {
            Header('Content-type:application/xml');
            echo $xml;
        }
    }

    protected function renderJsAlert($content, $return = false, $status_code = 200)
    {
        $this->beforeRenderJsAlert();
        $this->response->httpCode($status_code);
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

    protected function render($template_name, $params = [], $return = false, $status_code = 200)
    {
        $this->beforeRender();
        $this->response->httpCode($status_code);
        $params += ['controller' => $this];
        $params += $this->public_params;
        $this->public_params = $params;
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            $output = Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
            return $this->injectDebugBar($output);
        } else {
            ob_start();
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, $this->layout, $return, $js_files, $css_files);
            $output = ob_get_contents();
            ob_end_clean();
            @_echo($this->injectDebugBar($output));
            return '';
        }
    }

    protected function renderPartial($template_name, $params = [], $return = false, $status_code = 200)
    {
        $this->beforeRenderPartial();
        $this->response->httpCode($status_code);
        $params += ['controller' => $this];
        $params += $this->public_params;
        $this->public_params = $params;
        $js_files = Lb::app()->getJsFiles($this->controller_id, $template_name);
        $css_files = Lb::app()->getCssFiles($this->controller_id, $template_name);
        if ($return) {
            $output = Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
            return $this->injectDebugBar($output);
        } else {
            ob_start();
            Render::output($this->controller_id . DIRECTORY_SEPARATOR . $template_name, $params, '', $return, $js_files, $css_files);
            $output = ob_get_contents();
            ob_end_clean();
            @_echo($this->injectDebugBar($output));
            return '';
        }
    }

    /**
     * Inject debug bar to html output
     *
     * @param  $output
     * @return mixed
     */
    protected function injectDebugBar($output)
    {
        if (!($debugBarConfig = Lb::app()->getConfigByName('debugbar'))) {
            return $output;
        }
        if (empty($debugBarConfig['enabled'])) {
            return $output;
        }
        if (empty($debugBarConfig['baseUrl'])) {
            return $output;
        }
        if (empty($debugBarConfig['basePath'])) {
            return $output;
        }

        $debugBar = new StandardDebugBar();
        $debugBarRenderer = $debugBar->getJavascriptRenderer($debugBarConfig['baseUrl'], $debugBarConfig['basePath']);
        $debugBarComponent = $debugBarRenderer->renderHead() . $debugBarRenderer->render();
        $replace = <<<EOF
{$debugBarComponent}
</body>
EOF;
        return str_replace('</body>', $replace, $output);
    }

    protected function assign($param_name, $param_value)
    {
        $this->public_params[$param_name] = $param_value;
    }

    protected function get($param_name) 
    {
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

    public function error($err_msg, $tpl_name, $status_code)
    {
        // Declare Http Code
        $this->response->httpCode($status_code);

        $viewPath = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . "{$tpl_name}.php";
        if (file_exists($viewPath)) {
            $this->render(
                $tpl_name, [
                'title' => 'Exception',
                'err_msg' => $err_msg,
                ]
            );
        } else {
            echo $err_msg;
        }
    }

    /**
     * API文档
     */
    public function api()
    {
        $scan = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'controllers';
        $exclude = [];
        $api_doc_config = Lb::app()->getApiDocConfig();
        if ($api_doc_config) {
            if (isset($api_doc_config['scan']) && $api_doc_config['scan']) {
                $scan = $api_doc_config['scan'];
            }
            if (isset($api_doc_config['exclude']) && $api_doc_config['exclude']) {
                $exclude = $api_doc_config['exclude'];
            }
        }
        header('Content-Type: application/json');
        $swagger = \Swagger\scan(
            $scan, [
            'exclude' => $exclude,
            ]
        );
        echo $swagger;
    }
}
