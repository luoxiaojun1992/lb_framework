<?php

namespace lb\components\middleware;

use lb\components\Auth;

class AuthMiddleware extends BaseMiddleware
{
    public function returnAction($authType, $restConfig)
    {
        switch($authType) {
            case Auth::AUTH_TYPE_BASIC:
                if (!Auth::authBasic($restConfig[2][0], $restConfig[2][1])) {
                    return false;
                }
                break;
            case Auth::AUTH_TYPE_OAUTH:
                break;
            case Auth::AUTH_TYPE_QUERY_STRING:
                if (!Auth::authQueryString($restConfig[2][0], $restConfig[2][1])) {
                    return false;
                }
                break;
            default:
        }

        return parent::returnAction();
    }
}
