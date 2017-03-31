<?php

namespace lb\components\middleware;

use lb\components\Auth;

class AuthMiddleware extends BaseMiddleware
{
    public function runAction($params, $successCallback, $failureCallback)
    {
        $authResult = true;
        $restConfig = $params['rest_config'];
        switch($params['auth_type']) {
            case Auth::AUTH_TYPE_BASIC:
                if (!Auth::authBasic($restConfig[2][0], $restConfig[2][1])) {
                    $authResult = false;
                }
                break;
            case Auth::AUTH_TYPE_OAUTH:
                break;
            case Auth::AUTH_TYPE_QUERY_STRING:
                if (!Auth::authQueryString($restConfig[2][0], $restConfig[2][1])) {
                    $authResult = false;
                }
                break;
            default:
        }

        if ($authResult) {
            $successCallback && call_user_func($successCallback);
        } else {
            $failureCallback && call_user_func($failureCallback);
            return false;
        }

        return parent::runAction($params, $successCallback, $failureCallback);
    }
}
