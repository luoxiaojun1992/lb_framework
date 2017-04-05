<?php

namespace lb\components\middleware;

use lb\components\Auth;
use lb\components\request\RequestContract;

class AuthMiddleware extends BaseMiddleware
{
    public function runAction($params, $successCallback, $failureCallback)
    {
        $authResult = true;
        /** @var RequestContract $request */
        $request = $params['request'];
        $restConfig = $params['rest_config'];
        switch($params['auth_type']) {
            case Auth::AUTH_TYPE_BASIC:
                if (!Auth::authBasic($restConfig[2][0], $restConfig[2][1], $request)) {
                    $authResult = false;
                }
                break;
            case Auth::AUTH_TYPE_OAUTH:
                break;
            case Auth::AUTH_TYPE_QUERY_STRING:
                if (!Auth::authQueryString($restConfig[2][0], $restConfig[2][1], $request)) {
                    $authResult = false;
                }
                break;
            default:
        }

        if ($authResult) {
            $successCallback && call_user_func($successCallback);
        } else {
            $failureCallback && call_user_func($failureCallback);
        }

        $this->runNextMiddleware();
    }
}
