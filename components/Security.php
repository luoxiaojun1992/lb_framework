<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/8
 * Time: 下午10:53
 * Lb framework security component file
 */

namespace lb\components;

use lb\Lb;

class Security
{
    protected static $getfilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    protected static $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

    public static function inputFilter()
    {
        foreach ($_REQUEST as $request_name => $request_value) {
            $_REQUEST[$request_name] = self::getFilteredInput($request_value);
        }
    }

    protected static function getFilteredInput($input_value)
    {
        if (!is_array($input_value)) {
            $input_value = trim($input_value);
            $input_value = strip_tags($input_value);
        }
        $filter_name = strtolower(Lb::app()->getRequestMethod()) . 'filter';
        if (property_exists('self', $filter_name)) {
            $filter = self::$$filter_name;
            $input_value = self::filter($input_value, $filter);
        }
        return $input_value;
    }

    protected static function filter($input_value, $filter){
        $tmp_value = $input_value;
        if(is_array($tmp_value)) {
            $tmp_value = implode('', $tmp_value);
        }
        if (preg_match("/".$filter."/is", $tmp_value) == true){
            $input_value = preg_replace("/".$filter."/is", '', $tmp_value);
        }
        return $input_value;
    }

    public static function generateCsrfToken()
    {
        return md5(uniqid(rand(), true));
    }

    public static function validCsrfToken()
    {
        if (strtolower(Lb::app()->getRequestMethod()) == 'post') {
            $session_csrf_token = Lb::app()->getSession('csrf_token');
            if (!$session_csrf_token) {
                Lb::app()->stop();
            } else {
                if ($session_csrf_token != Lb::app()->getParam('csrf_token')) {
                    Lb::app()->stop();
                }
            }
        } else {
            Lb::app()->setSession('csrf_token', Lb::app()->getCsrfToken());
        }
    }

    public static function sqlFilter($sql_statement)
    {
       return addslashes($sql_statement);
    }
}
