<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午9:24
 * Lb framework render component file
 */

namespace lb\components;

use lb\components\assets\Javascript;
use lb\components\assets\Css;
use lb\Lb;

class Render
{
    protected static $root_dir = '';

    public static function getViewDir()
    {
        $root_dir = static::$root_dir ? : Lb::app()->getRootDir();
        return $root_dir . DIRECTORY_SEPARATOR . 'views';
    }

    public static function getLayoutDir()
    {
        $root_dir = static::$root_dir ? : Lb::app()->getRootDir();
        return $root_dir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts';
    }

    public static function getViewPath($template_name)
    {
        $view_file_path = '';
        $views_dir = static::getViewDir();
        if (is_dir($views_dir)) {
            $view_file_path = $views_dir . DIRECTORY_SEPARATOR . $template_name . '.php';
        }
        return $view_file_path;
    }

    public static function getLayoutPath($layout_name)
    {
        $layout_file_path = '';
        $layouts_dir = static::getLayoutDir();
        if (is_dir($layouts_dir)) {
            $layout_file_path = $layouts_dir . DIRECTORY_SEPARATOR . $layout_name . '.php';
        }
        return $layout_file_path;
    }

    public static function output($template_name, $params, $layout_name, $return = false, $js_files = [], $css_files = [])
    {
        $root_dir = Lb::app()->getRootDir();
        if ($root_dir) {
            self::$root_dir = $root_dir;

            $view_file_path = static::getViewPath($template_name);
            if (file_exists($view_file_path)) {
                foreach ($params as $param_name => $param_value) {
                    $$param_name = $param_value;
                }
                $js_html = '';
                if ($js_files) {
                    $js_html = '<script src="' . Javascript::dump($js_files) . '"></script>';
                }
                $css_html = '';
                if ($css_files) {
                    $css_html = '<link rel="stylesheet" href="' . Css::dump($css_files) . '" />';
                }
                $layout_file_path = self::getLayoutPath($layout_name);
                if (file_exists($layout_file_path)) {
                    ob_start();
                    include_once($view_file_path);
                    $content = ob_get_contents();
                    ob_end_clean();
                    if ($return) {
                        ob_start();
                        include_once($layout_file_path);
                        $return_content = ob_get_contents();
                        ob_end_clean();
                        return $return_content;
                    } else {
                        include_once($layout_file_path);
                        Lb::app()->stop();
                    }
                }
                if ($return) {
                    ob_start();
                    include_once($view_file_path);
                    $return_content = ob_get_contents();
                    ob_end_clean();
                    return $return_content;
                } else {
                    include_once($view_file_path);
                    Lb::app()->stop();
                }
            }
        }
    }
}
