<?php

use \lb\Lb;

/**
 * No Exception Echo
 */
function _echo($var)
{
    if (isset($var)) {
        echo $var;
    }
}

/**
 * Get Configuration By Name
 */
function config($config_name)
{
    return Lb::app()->getConfigByName($config_name);
}

/**
 * Get Environment Value By Name
 */
function env($env_name)
{
    return Lb::app()->getEnv($env_name);
}

