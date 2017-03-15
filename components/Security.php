<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\error_handlers\HttpException;
use lb\components\helpers\ArrayHelper;
use lb\components\helpers\HtmlHelper;
use lb\Lb;

class Security extends BaseClass
{
    protected static $getfilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

    public static function inputFilter(&$params = [])
    {
        if ($params) {
            foreach ($params as $request_name => $request_value) {
                $params[$request_name] = static::getFilteredInput($request_value);;
            }
        } else {
            $_REQUEST && self::inputFilter($_REQUEST);
            $_GET && self::inputFilter($_GET);
            $_POST && self::inputFilter($_POST);
            $_COOKIE && self::inputFilter($_COOKIE);
        }
    }

    /**
     * @param $input_value
     * @return array|mixed
     */
    protected static function getFilteredInput($input_value)
    {
        if (is_array($input_value)) {
            foreach ($input_value as $k => $value) {
                $input_value[$k] = self::getFilteredInput($value);
            }
        } else {
            $input_value = static::removeSqlInjection($input_value);
            $input_value = HtmlHelper::encode($input_value);
        }
        return $input_value;
    }

    /**
     * @param $value
     * @return array|mixed
     */
    public static function removeSqlInjection($value)
    {
        if (property_exists(
            get_called_class(),
            $filter_name = strtolower(Lb::app()->getRequestMethod()) . 'filter'
        )) {
            $filter = "/" . static::$$filter_name . "/is";
            $value = preg_match($filter, $value) == true ?
                preg_replace($filter, '', $value) : $value;
        }
        return $value;
    }

    /**
     * @return string
     */
    public static function generateCsrfToken()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * @param $controller
     * @param $action
     * @throws HttpException
     */
    public static function validCsrfToken($controller, $action)
    {
        $csrf_config = Lb::app()->getCsrfConfig();
        if (!isset($csrf_config['filter']['controllers'][$controller][$action]) || !$csrf_config['filter']['controllers'][$controller][$action]) {
            if (strtolower(Lb::app()->getRequestMethod()) == 'post') {
                $session_csrf_token = Lb::app()->getSession(implode('_', ['csrf_token', $controller, $action]));
                $request_csrf_token = Lb::app()->getParam('csrf_token');
                if ($session_csrf_token && $request_csrf_token) {
                    if ($session_csrf_token != $request_csrf_token) {
                        throw new HttpException('Csrf token invalid.', 403);
                    }
                } else {
                    throw new HttpException('Csrf token missing.', 403);
                }
            }
            if (!(Lb::app()->isAjax() && strtolower(Lb::app()->getRequestMethod()) == 'post')) {
                Lb::app()->setSession(implode('_', ['csrf_token', $controller, $action]), Lb::app()->getCsrfToken());
            }
        }
    }

    /**
     * @param $controller
     * @param $action
     */
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

    /**
     * @param $controller
     * @param $action
     */
    public static function x_frame_options($controller, $action)
    {
        if (isset(Lb::app()->containers['config'])) {
            $config = Lb::app()->containers['config'];
            $x_frame_options = $config->get('x_frame_options');
            if ($x_frame_options) {
                if (isset($x_frame_options[$controller][$action])) {
                    header("X-Frame-Options: {$x_frame_options[$controller][$action]}");
                } else if (isset($x_frame_options['common'])) {
                    header("X-Frame-Options: {$x_frame_options['common']}");
                } else {
                    header("X-Frame-Options: DENY");
                }
                return;
            }
        }
        header("X-Frame-Options: DENY");
    }

    /**
     * @param $controller
     * @param $action
     */
    public static function x_xss_protection($controller, $action)
    {
        if (isset(Lb::app()->containers['config'])) {
            $config = Lb::app()->containers['config'];
            $x_xss_protection = $config->get('x_xss_protection');
            if ($x_xss_protection) {
                if (isset($x_xss_protection[$controller][$action])) {
                    header("X-XSS-Protection: {$x_xss_protection[$controller][$action]}");
                } else if (isset($x_xss_protection['common'])) {
                    header("X-XSS-Protection: {$x_xss_protection['common']}");
                } else {
                    header("X-XSS-Protection: 1");
                }
                return;
            }
        }
        header("X-XSS-Protection: 1");
    }

    /**
     * @param $controller
     * @param $action
     * @throws HttpException
     */
    public static function ipFilter($controller, $action)
    {
        if (isset(Lb::app()->containers['config'])) {
            $config = Lb::app()->containers['config'];
            $filter = $config->get('filter');
            if ($filter) {
                if (isset($filter['ip'][$controller][$action]) && is_array($filter['ip'][$controller][$action]) && ArrayHelper::array_depth($filter['ip'][$controller][$action]) == 1) {
                    if (in_array(Lb::app()->getClientAddress(), $filter['ip'][$controller][$action])) {
                        throw new HttpException('IP Forbidden', 403);
                    }
                }
            }
        }
    }

    /**
     * @param $password
     * @return bool|string
     */
    public static function generatePasswordHash($password)
    {
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT);
        }

        return md5($password);
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        if (function_exists('password_verify')) {
            return password_verify($password, $hash);
        }

        return md5($password) == $hash;
    }
}
