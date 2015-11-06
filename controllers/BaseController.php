<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午3:12
 * Lb framework base controller file
 */

namespace lb\controllers;

use lb\Lb;

class BaseController
{
    protected $layout = 'default';

    protected function render($template_name, $params)
    {
        if (isset(Lb::app()->config['root_dir'])) {
            $root_dir = Lb::app()->config['root_dir'];
            $views_dir = $root_dir . DIRECTORY_SEPARATOR . 'views';
            if (is_dir($views_dir)) {
                $view_file_path = $views_dir . DIRECTORY_SEPARATOR . $template_name . '.php';
                if (file_exists($view_file_path)) {
                    foreach ($params as $param_name => $param_value) {
                        $$param_name = $param_value;
                    }
                    include_once($view_file_path);
                }
            }
        }
    }
}
