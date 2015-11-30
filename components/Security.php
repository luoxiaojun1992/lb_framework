<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/8
 * Time: 下午10:53
 * Lb framework security component file
 */

namespace lb\components;

use lb\components\helpers\ArrayHelper;
use lb\Lb;

class Security
{
    protected static $getfilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $xssfilters = [
        "/\\s+/",
        "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>g)/isU",
    ];

    public static function inputFilter()
    {
        foreach ($_REQUEST as $request_name => $request_value) {
            $_REQUEST[$request_name] = static::getFilteredInput($request_value);
        }
    }

    protected static function getFilteredInput($input_value)
    {
        if (!is_array($input_value)) {
            foreach (static::$xssfilters as $xssfilter) {
                if (preg_match($xssfilter, $input_value) == true) {
                    $input_value = preg_replace($xssfilter, '', $input_value);
                }
            }
            $input_value = trim($input_value);
            $input_value = htmlspecialchars($input_value);
            $input_value = addslashes($input_value);
        } else {
            foreach ($input_value as $key => $value) {
                if (!is_array($value)) {
                    foreach (static::$xssfilters as $xssfilter) {
                        if (preg_match($xssfilter, $value) == true) {
                            $value = preg_replace($xssfilter, '', $value);
                        }
                    }
                    $value = trim($value);
                    $value = htmlspecialchars($value);
                    $value = addslashes($value);
                    $input_value[$key] = $value;
                }
            }
        }
        $filter_name = strtolower(Lb::app()->getRequestMethod()) . 'filter';
        if (property_exists(get_called_class(), $filter_name)) {
            $filter = static::$$filter_name;
            $input_value = static::filter($input_value, $filter);
        }
        return $input_value;
    }

    protected static function filter($input_value, $filter){
        if (is_array($input_value)) {
            foreach ($input_value as $key => $value) {
                if (!is_array($value)) {
                    if (preg_match("/" . $filter . "/is", $value) == true) {
                        $value = preg_replace("/" . $filter . "/is", '', $value);
                        $input_value[$key] = $value;
                    }
                }
            }
        } else {
            if (preg_match("/".$filter."/is", $input_value) == true) {
                $input_value = preg_replace("/".$filter."/is", '', $input_value);
            }
        }
        return $input_value;
    }

    public static function generateCsrfToken()
    {
        return md5(uniqid(rand(), true));
    }

    public static function validCsrfToken($controller, $action)
    {
        if (strtolower(Lb::app()->getRequestMethod()) == 'post') {
            $session_csrf_token = Lb::app()->getSession(implode('_', ['csrf_token', $controller, $action]));
            $request_csrf_token = Lb::app()->getParam('csrf_token');
            if ($session_csrf_token && $request_csrf_token) {
                if ($session_csrf_token != $request_csrf_token) {
                    Lb::app()->stop();
                }
            } else {
                Lb::app()->stop();
            }
        }
        Lb::app()->setSession(implode('_', ['csrf_token', $controller, $action]), Lb::app()->getCsrfToken());
    }

    public static function cors($controller, $action)
    {
        if (isset(Lb::app()->containers['config'])) {
            $config = Lb::app()->containers['config'];
            $cors = $config->get('cors');
            if ($cors) {
                if (isset($cors[$controller][$action]) && $cors[$controller][$action] == true) {
                    header('Access Control Allow Origin: *');
                }
            }
        }
    }

    public static function ipFilter($controller, $action)
    {
        if (isset(Lb::app()->containers['config'])) {
            $config = Lb::app()->containers['config'];
            $filter = $config->get('filter');
            if ($filter) {
                if (isset($filter['ip'][$controller][$action]) && is_array($filter['ip'][$controller][$action]) && ArrayHelper::array_depth($filter['ip'][$controller][$action]) == 1) {
                    if (in_array(Lb::app()->getClientAddress(), $filter['ip'][$controller][$action])) {
                        Lb::app()->stop('IP Forbidden');
                    }
                }
            }
        }
    }
}
