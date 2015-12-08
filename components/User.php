<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/8
 * Time: 13:39
 * Lb framework user component file
 */

namespace lb\components;

use lb\Lb;

class User
{
    public static function login($username, $user_id, $remember_token = '', $timeout = 0)
    {
        Lb::app()->setSession('username', $username);
        Lb::app()->setSession('user_id', $user_id);

        // Remember Me
        if ($remember_token && $timeout) {
            setcookie('username', $username, $timeout, null, null, null, true);
            setcookie('remember_token', $remember_token, $timeout, null, null, null, true);
        }
    }

    public static function loginRequired($redirect_url)
    {
        if (!Lb::app()->getSession('username') || !Lb::app()->getSession('user_id')) {
            Lb::app()->redirect($redirect_url);
        }
    }

    public static function isGuest()
    {
        return Lb::app()->getSession('username') && Lb::app()->getSession('user_id');
    }
}
