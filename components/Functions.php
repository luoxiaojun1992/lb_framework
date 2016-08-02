<?php

use lb\Lb;

if (!function_exists('_echo')) {
    /**
     * No Exception Echo
     */
    function _echo($var)
    {
        if (isset($var)) {
            echo $var;
        }
    }
}

if (!function_exists('config')) {
    /**
     * Get Configuration By Name
     */
    function config($config_name)
    {
        return Lb::app()->getConfigByName($config_name);
    }
}

if (!function_exists('env')) {
    /**
     * Get Environment Value By Name
     */
    function env($env_name)
    {
        return Lb::app()->getEnv($env_name);
    }
}

if (!function_exists('file_cache')) {
    /**
     * File Cache Operations
     */
    function file_cache($key, $value = null)
    {

    }
}

if (!function_exists('redis_cache')) {
    /**
     * Redis Cache Operations
     */
    function redis_cache($key, $value = null)
    {

    }
}

if (!function_exists('memcache')) {
    /**
     * Memcache Operations
     */
    function memcache($key, $value = null)
    {

    }
}

