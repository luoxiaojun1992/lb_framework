<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午7:50
 * Lb framework error level file
 */

namespace lb\components\error_handlers;

use lb\BaseClass;

class Level extends BaseClass
{
    public static $env = 'dev';

    public static function set()
    {
        if (defined('LB_ENV')) {
            static::$env = strtolower(LB_ENV);
        }
        static::error_reporting();
    }

    public static function change($env = 'dev')
    {
        static::$env = $env;
        static::error_reporting();
    }

    protected static function error_reporting()
    {
        switch (static::$env) {
            case 'stage':
            case 'production':
                //报告运行时错误
                error_reporting(0);
                break;
            case 'dev':
                //报告所有错误
                error_reporting(E_ALL);
                break;
            default:
                //报告所有错误
                error_reporting(E_ALL);
        }
    }

    public static function get()
    {
        return static::$env;
    }
}
