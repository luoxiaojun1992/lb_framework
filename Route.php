<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 下午1:26
 * Lb framework route file
 */

namespace lb;

class Route
{
    public static function getControllerAction()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $query_string = $_SERVER['QUERY_STRING'];
        $real_uri = str_replace($query_string, '', $request_uri);
        var_dump($request_uri);
        var_dump($query_string);
        var_dump($real_uri);
    }
}
