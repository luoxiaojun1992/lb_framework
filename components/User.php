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
            Lb::app()->setHeaderCookie('username', $username, $timeout, null, null, null, true);
            Lb::app()->setHeaderCookie('remember_token', $remember_token, $timeout, null, null, null, true);
        }
    }

    public static function loginRequired($redirect_url)
    {
        if (!Lb::app()->getSession('username') || !Lb::app()->getSession('user_id')) {
            if (Lb::app()->isAction()) {
                $http_port = Lb::app()->getHttpPort();
                if (stripos(Lb::app()->createAbsoluteUrl(Lb::app()->getUri() . Lb::app()->getQueryString(), [], true, $http_port), $redirect_url) === false || stripos(Lb::app()->createAbsoluteUrl(Lb::app()->getUri() . Lb::app()->getQueryString(), [], false, $http_port), $redirect_url) === false) {
                    Lb::app()->redirect($redirect_url);
                }
            }
        }
    }

    public static function isGuest()
    {
        return !Lb::app()->getSession('username') || !Lb::app()->getSession('user_id');
    }

    public static function logOut()
    {
        Lb::app()->delSessions(['username', 'user_id']);
        Lb::app()->delCookies(['username', 'remember_token']);
    }
}
