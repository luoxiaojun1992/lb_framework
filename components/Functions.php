<?php

use lb\Lb;
use lb\components\consts\ErrorMsg;
use lb\components\error_handlers\VariableException;

if (!function_exists('_echo')) {
    /**
     * No Exception Echo
     */
    function _echo($var)
    {
        if (isset($var)) {
            echo $var;
        } else {
            throw new VariableException(ErrorMsg::errorMsg[ErrorMsg::VARIABLE_NOT_DEFINED], ErrorMsg::ERROR_VARIABLE_NOT_DEFINED);
        }
    }
}

if (!function_exists('config')) {
    /**
     * Get Configuration By Name
     */
    function config($config_name, $default = null)
    {
        return Lb::app()->getConfigByName($config_name) ? : $default;
    }
}

if (!function_exists('env')) {
    /**
     * Get Environment Value By Name
     */
    function env($env_name, $default = null)
    {
        return Lb::app()->getEnv($env_name) ? : $default;
    }
}

if (!function_exists('file_cache')) {
    /**
     * File Cache Operations
     */
    function file_cache($key, $value = null, $cache_time = 86400)
    {
        if (!$value) {
            return Lb::app()->fileCacheGet($key);
        } else {
            Lb::app()->fileCacheSet($key, $value, $cache_time);
        }

        return true;
    }
}

if (!function_exists('redis_cache')) {
    /**
     * Redis Cache Operations
     */
    function redis_cache($key, $value = null, $expiration = 0)
    {
        if (!$value) {
            return Lb::app()->redisGet($key);
        } else {
            Lb::app()->redisSet($key, $value, $expiration);
        }
    }
}

if (!function_exists('memcache')) {
    /**
     * Memcache Operations
     */
    function memcache($key, $value = null, $expiration = null)
    {
        if (!$value) {
            return Lb::app()->memcacheGet($key);
        } else {
            Lb::app()->memcacheSet($key, $value, $expiration);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dump data
     */
    function dd()
    {
        array_map(function($x)
        {
            dump($x);
        }, func_get_args());

        Lb::app()->stop();
    }
}

if (!function_exists('aop')) {
    /**
     * AOP Register
     *
     * @param array $callable
     * @param Closure $closure
     */
    function aop(array $callable, \Closure $closure)
    {
        /** @var \lb\components\listeners\AopListener $aopListener */
        $aopListener = \lb\components\listeners\AopListener::component();
        Lb::app()->on(
            \lb\components\consts\Event::AOP_EVENT . '_' . implode('@', $callable),
            $aopListener,
            ['closure' => $closure]
        );
    }
}
