<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\helpers\HashHelper;
use lb\components\request\RequestContract;
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
     * @param $request RequestContract
     * @return bool
     */
    public static function authBasic($user, $password, $request = null)
    {
        return ($request ? $request->getBasicAuthUser() : Lb::app()->getBasicAuthUser()) == $user &&
        HashHelper::hash($request ? $request->getBasicAuthPassword() : Lb::app()->getBasicAuthPassword()) == $password;
    }

    /**
     * Query String Authentication
     *
     * @param $authKey
     * @param $accessToken
     * @param $request RequestContract
     * @return bool
     */
    public static function authQueryString($authKey, $accessToken, $request = null)
    {
        return HashHelper::hash($request ? $request->getParam($authKey) : Lb::app()->getParam($authKey)) == $accessToken;
    }
}
