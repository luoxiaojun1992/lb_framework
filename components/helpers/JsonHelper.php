<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 15:43
 * Lb framework json helper component file
 */

namespace lb\components\helpers;

class JsonHelper
{
    public static function encode($data)
    {
        $json = '';
        if (is_array($data)) {
            $json = json_encode($data);
        } else {
            if (!is_object($data) && !is_resource($data)) {
                $json = json_encode([$data]);
            }
        }
        return $json;
    }
}
