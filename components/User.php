<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\request\RequestContract;
use lb\components\response\ResponseContract;
use lb\Lb;

class User extends BaseClass
{
    public static function login($username, $user_id, $remember_token = '', $timeout = 0)
    {
        Lb::app()->setSession('username', $username);
        Lb::app()->setSession('user_id', $user_id);

        // Remember Me
        if ($remember_token && $timeout) {
            Lb::app()->setCookie('username', $username, $timeout, null, null, null, true);
            Lb::app()->setCookie('remember_token', $remember_token, $timeout, null, null, null, true);
        }
    }

    /**
     * @param $redirect_url
     * @param $request RequestContract
     * @param $response ResponseContract
     */
    public static function loginRequired($redirect_url, $request = null, $response = null)
    {
        if (!($request ? $request->getSession('username') : Lb::app()->getSession('username')) 
            || !($request ? $request->getSession('user_id') : Lb::app()->getSession('user_id'))
        ) {
            if (Lb::app()->isAction($request)) {
                $http_port = Lb::app()->getHttpPort();
                $requestUri = $request ? $request->getUri() : Lb::app()->getUri();
                if (stripos(Lb::app()->createAbsoluteUrl($requestUri, [], true, $http_port, $request), $redirect_url) === false 
                    || stripos(Lb::app()->createAbsoluteUrl($requestUri, [], false, $http_port, $request), $redirect_url) === false
                ) {
                    Lb::app()->redirect($redirect_url, true, null, $response);
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
        Lb::app()->delHeaderCookies(['username', 'remember_token']);
    }
}
