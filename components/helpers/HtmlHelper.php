<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/10
 * Time: 13:10
 * Lb framework html helper component file
 */

namespace lb\components\helpers;

use lb\Lb;

class HtmlHelper
{
    protected static function format_tag($tag)
    {
        return trim(strtolower($tag));
    }

    public static function compress($html_code)
    {
        $segments = preg_split("/(<[^>]+?>)/si",$html_code, -1,PREG_SPLIT_NO_EMPTY| PREG_SPLIT_DELIM_CAPTURE);
        $compressed = [];
        $stack = [];
        $tag = '';
        $half_open = ['meta','input','link','img','br'];
        $cannot_compress = ['pre','code','script','style'];
        foreach ($segments as $seg) {
            if (trim($seg) === '') {
                continue;
            }
            if (preg_match("!<([a-z0-9]+)[^>]*?/>!si",$seg, $match) || preg_match("~<![^>]*>~", $seg)) {
                //文档声明和注释，注释也不能删除，如<!--ie条件-->
                $compressed[] = $seg;
            } else if (preg_match("!</([a-z0-9]+)[^>]*?>!si",$seg,$match)) {
                $tag = static::format_tag($match[1]);
                if (count($stack) > 0 && $stack[count($stack)-1] == $tag) {
                    array_pop($stack);
                    $compressed[] = $seg;
                }
                //这里再最好加一段判断，可以用于修复错误的html
            } else if (preg_match("!<([a-z0-9]+)[^>]*?>!si",$seg,$match)) {
                $tag = static::format_tag($match[1]);
                //半闭合标签不需要入栈，如<br/>,<img/>
                if (!in_array($tag, $half_open)) {
                    array_push($stack,$tag);
                }
                $compressed[] = $seg;
            } else {
                $compressed[] = in_array($tag, $cannot_compress) ? $seg : preg_replace('!\s!', '', $seg);
            }
        }
        return join('',$compressed);
    }

    public static function setCache($cache_control, $offset)
    {
        Header("Cache-Control: {$cache_control}");
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        Header($ExpStr);
    }

    public static function encode($html)
    {
        return htmlspecialchars($html);
    }

    public static function decode($html)
    {
        return htmlspecialchars_decode($html);
    }

    public static function image($src, $alt = '', $options = [])
    {
        $image_tag_tpl = '<img src="%s" alt="%s"%s />';
        $option_str = '';
        if ($options) {
            $option_arr = [];
            foreach ($options as $key => $value) {
                if (is_string($key)) {
                    $option_arr[] = implode('=', [$key, '"' . $value . '"']);
                } else {
                    $option_arr[] = $value;
                }
            }
            $option_str = ' ' . implode(' ', $option_arr);
        }
        $cdnHost = Lb::app()->getCdnHost();
        if ($cdnHost) {
            if (!ValidationHelper::isUrl($src)) {
                $src = $cdnHost . $src;
            } else {
                $src = preg_replace('/^(http|https):\/\/.+?\//i', $cdnHost . '/', $src);
            }
        }
        return sprintf($image_tag_tpl, $src, $alt, $option_str);
    }

    public static function a($href, $content = '', $title = '', $target = '', $options = [])
    {
        $a_tag_tpl = '<a href="%s" title="%s" target="%s"%s>%s</a>';
        $option_str = '';
        if ($options) {
            $option_arr = [];
            foreach ($options as $key => $value) {
                if (is_string($key)) {
                    $option_arr[] = implode('=', [$key, '"' . $value . '"']);
                } else {
                    $option_arr[] = $value;
                }
            }
            $option_str = ' ' . implode(' ', $option_arr);
        }
        return sprintf($a_tag_tpl, $href, $title, $target, $option_str, $content);
    }
}
