<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\Lb;

class HtmlHelper extends BaseClass
{
    protected static $htmlPurifier;

    /**
     * Compress html
     *
     * @param $html_code
     * @return string
     */
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

    /**
     * Encode html
     *
     * @param $html
     * @return string
     */
    public static function encode($html)
    {
        return htmlspecialchars($html);
    }

    /**
     * Decode html
     *
     * @param $html
     * @return string
     */
    public static function decode($html)
    {
        return htmlspecialchars_decode($html);
    }

    /**
     * Generate image tag
     *
     * @param $src
     * @param string $alt
     * @param array $options
     * @return string
     */
    public static function image($src, $alt = '', $options = [])
    {
        $image_tag_tpl = '<img src="%s" alt="%s"%s />';
        $cdnHost = Lb::app()->getCdnHost();
        if ($cdnHost) {
            if (!ValidationHelper::isUrl($src)) {
                $src = $cdnHost . $src;
            } else {
                $src = preg_replace('/^(http|https):\/\/.+?\//i', $cdnHost . '/', $src);
            }
        }
        return sprintf($image_tag_tpl, $src, $alt, self::assembleTagOptions($options));
    }

    /**
     * Generate a tag
     *
     * @param $href
     * @param string $content
     * @param string $title
     * @param string $target
     * @param array $options
     * @return string
     */
    public static function a($href, $content = '', $title = '', $target = '', $options = [])
    {
        $a_tag_tpl = '<a href="%s" title="%s" target="%s"%s>%s</a>';
        return sprintf($a_tag_tpl, $href, $title, $target, self::assembleTagOptions($options), $content);
    }

    /**
     * Purify dirty html
     *
     * @param $dirtyHtml
     * @return mixed
     */
    public static function purify($dirtyHtml)
    {
        if (self::$htmlPurifier && self::$htmlPurifier instanceof \HTMLPurifier) {
            $purifier = self::$htmlPurifier;
        } else {
            $purifier = (self::$htmlPurifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault()));
        }
        return $purifier->purify($dirtyHtml);
    }

    protected static function format_tag($tag)
    {
        return trim(strtolower($tag));
    }

    protected static function assembleTagOptions(array $options = [])
    {
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

        return $option_str;
    }
}
