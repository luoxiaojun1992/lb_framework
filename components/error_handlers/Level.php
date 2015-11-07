<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午7:50
 * Lb framework error level file
 */

namespace lb\components\error_handlers;

class Level
{
    public static function set($env = 'dev')
    {
        if (defined('LB_ENV')) {
            $env = strtolower(LB_ENV);
        }
        switch ($env) {
            case 'production':
                //报告运行时错误
                error_reporting(E_ERROR | E_WARNING | E_PARSE);
                break;
            case 'dev':
                //报告所有错误
                error_reporting(E_ALL);
                break;
            default:
                break;
        }
    }
}
