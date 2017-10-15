<?php

namespace lb\components\helpers;

use lb\BaseClass;

class StringHelper extends BaseClass
{
    public static function replace_with_stars($str, $char = '*')
    {
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)) {
            $len = mb_strlen($str, 'UTF-8');
            if ($len < 3) {
                return $str;
            } elseif ($len < 5) {
                $slen = 1;
            } elseif ($len < 7) {
                $slen = 2;
            } elseif ($len < 9) {
                $slen = 3;
            } elseif ($len < 13) {
                $slen = 4;
            } elseif ($len < 16) {
                $slen = 5;
            } elseif ($len < 19) {
                $slen = 6;
            } else {
                $slen = 7;
            }
            $pos = ($len - $slen) % 2 == 0 ? (($len - $slen) / 2) : (intval(($len - $slen) / 2) + 1);
            $rt = mb_substr($str, 0, $pos, 'UTF-8') . str_repeat($char, $slen) . mb_substr($str, -1 * ($len - ($pos + $slen)), $len, 'UTF-8');
            return $rt;
        } else {
            $len = strlen($str);
            if ($len < 5) {
                return $str;
            } elseif ($len < 7) {
                $slen = 2;
            } elseif ($len < 9) {
                $slen = 3;
            } elseif ($len < 13) {
                $slen = 4;
            } else {
                $slen = 5;
            }
            $pos = ($len - $slen) % 2 == 0 ? (($len - $slen) / 2) : (intval(($len - $slen) / 2) + 1);
            $rt = substr($str, 0, $pos) . str_repeat($char, $slen) . substr($str, -1 * ($len - ($pos + $slen)));
            return $rt;
        }
    }

    /**
     * 半角和全角转换函数，第二个参数如果是0,则是半角到全角；如果是1，则是全角到半角
     *
     * @param  $str
     * @param  $args2
     * @return bool
     */
    public static function SBC_DBC($str, $args2 = 0)
    {
        $DBC = [
            '０' , '１' , '２' , '３' , '４' ,
            '５' , '６' , '７' , '８' , '９' ,
            'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
            'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
            'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
            'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
            'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
            'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
            'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
            'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
            'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
            'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
            'ｙ' , 'ｚ' , '－' , '　'  , '：' ,
            '．' , '，' , '／' , '％' , '＃' ,
            '！' , '＠' , '＆' , '（' , '）' ,
            '＜' , '＞' , '＂' , '＇' , '？' ,
            '［' , '］' , '｛' , '｝' , '＼' ,
            '｜' , '＋' , '＝' , '＿' , '＾' ,
            '￥' , '￣' , '｀'
        ];
        $SBC = [ //半角
            '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z', 'a', 'b', 'c', 'd',
            'e', 'f', 'g', 'h', 'i',
            'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x',
            'y', 'z', '-', ' ', ':',
            '.', ',', '/', '%', '#',
            '!', '@', '&', '(', ')',
            '<', '>', '"', '\'','?',
            '[', ']', '{', '}', '\\',
            '|', '+', '=', '_', '^',
            '$', '~', '`'
        ];
        if($args2==0) {
            return str_replace($SBC, $DBC, $str);  //半角到全角
        }
        if($args2==1) {
            return str_replace($DBC, $SBC, $str);  //全角到半角
        } else {
            return $str;
        }
    }

    public static function isCapital($char)
    {
        $ascii = ord($char);
        return $ascii >= ord('A') && $ascii <= ord('Z');
    }

    public static function camel($str, $delimiter = '_')
    {
        if (strpos($str, $delimiter) !== false) {
            $tempArr = explode($delimiter, $str);
            foreach ($tempArr as $key => $item) {
                $tempArr[$key] = ucfirst(strtolower($item));
            }
            $str = implode('', $tempArr);
        } else {
            $str = ucfirst($str);
        }

        return $str;
    }

    public static function underLine($str)
    {
        $capitals = [];
        for ($i = 0; $i < mb_strlen($str, 'UTF8'); ++$i) {
            if (self::isCapital($str[$i])) {
                $capitals[] = $str[$i];
            }
        }

        foreach ($capitals as $capital) {
            $str = str_replace($capital, '_' . strtolower($capital), $str);
        }

        return trim($str, '_');
    }
}
