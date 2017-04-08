<?php

namespace lb\components\helpers;

use lb\BaseClass;

class EncodeHelper extends BaseClass
{
    /**
     * @param $str
     * @return string
     */
    public static function base64Encode($str)
    {
        return base64_encode($str);
    }

    /**
     * @param $encodedStr
     * @return string
     */
    public static function base64Decode($encodedStr)
    {
        return base64_decode($encodedStr);
    }

    /**
     * @param $url
     * @return string
     */
    public static function urlEncode($url)
    {
        return urlencode($url);
    }

    /**
     * @param $encodedUrl
     * @return string
     */
    public static function urlDecode($encodedUrl)
    {
        return urldecode($encodedUrl);
    }
}
