<?php

namespace lb\components;

use lb\BaseClass;
use lb\Lb;

class Auth extends BaseClass
{
    // Authentication Type
    const AUTH_TYPE_BASIC = 1;
    const AUTH_TYPE_OAUTH = 2;
    const AUTH_TYPE_QUERY_STRING = 3;

    /**
     * Basic Authentication
     *
     * @param $user
     * @param $password
     * @return bool
     */
    public static function authBasic($user, $password)
    {
        return Lb::app()->getBasicAuthUser() == $user && md5(Lb::app()->getBasicAuthPassword()) == $password;
    }

    /**
     * Query String Authentication
     *
     * @param $authKey
     * @param $accessToken
     * @return bool
     */
    public static function authQueryString($authKey, $accessToken)
    {
        return md5(Lb::app()->getParam($authKey)) == $accessToken;
    }
}
