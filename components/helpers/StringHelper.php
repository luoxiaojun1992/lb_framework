<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/3/10
 * Time: 17:10
 * Lb framework string helper component file
 */

namespace lb\components\helpers;

use lb\BaseClass;

class StringHelper extends BaseClass
{
    public static function replace_with_stars($str, $char = '*')
    {
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)) {
            $len = mb_strlen($str, 'UTF-8');
            if ($len < 3)
                return $str;
            elseif ($len < 5)
                $slen = 1;
            elseif ($len < 7)
                $slen = 2;
            elseif ($len < 9)
                $slen = 3;
            elseif ($len < 13)
                $slen = 4;
            elseif ($len < 16)
                $slen = 5;
            elseif ($len < 19)
                $slen = 6;
            else
                $slen = 7;
            $pos = ($len - $slen) % 2 == 0 ? (($len - $slen) / 2) : (intval(($len - $slen) / 2) + 1);
            $rt = mb_substr($str, 0, $pos, 'UTF-8') . str_repeat($char, $slen) . mb_substr($str, -1 * ($len - ($pos + $slen)), $len, 'UTF-8');
            return $rt;
        } else {
            $len = strlen($str);
            if ($len < 5)
                return $str;
            elseif ($len < 7)
                $slen = 2;
            elseif ($len < 9)
                $slen = 3;
            elseif ($len < 13)
                $slen = 4;
            else
                $slen = 5;
            $pos = ($len - $slen) % 2 == 0 ? (($len - $slen) / 2) : (intval(($len - $slen) / 2) + 1);
            $rt = substr($str, 0, $pos) . str_repeat($char, $slen) . substr($str, -1 * ($len - ($pos + $slen)));
            return $rt;
        }
    }
}
