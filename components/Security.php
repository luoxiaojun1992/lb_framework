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

    //Remove the exploer'bug XSS
    protected static function RemoveXSS($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }
        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(�{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;
    }

    protected static function getFilteredInput($input_value)
    {
        if (!is_array($input_value)) {
            foreach (static::$xssfilters as $xssfilter) {
                if (preg_match($xssfilter, $input_value) == true) {
                    $input_value = preg_replace($xssfilter, '', $input_value);
                    $input_value = static::RemoveXSS($input_value);
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
                            $value = static::RemoveXSS($value);
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
