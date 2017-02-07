<?php

namespace lb\components\helpers;

use lb\BaseClass;

class JsonHelper extends BaseClass
{
    public static function encode($data)
    {
        $json = '';
        if (is_array($data)) {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            if (!is_object($data) && !is_resource($data)) {
                $json = json_encode([$data], JSON_UNESCAPED_UNICODE);
            }
        }
        return $json;
    }

    public static function decode($json)
    {
        $result = [$json];
        if (static::is_json($json)) {
            $result = json_decode($json, true);
        }
        return $result;
    }

    public static function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
