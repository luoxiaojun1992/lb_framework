<?php

namespace lb\components\middleware;

use lb\components\Response;
use lb\Lb;

class RequestMethodFilter extends BaseMiddleware
{
    public function runAction($params, $successCallback, $failureCallback)
    {
        $result = true;
        $requestMethod = trim($params['request_method']);
        if ($requestMethod != '*' &&
            strtolower($requestMethod) != strtolower(Lb::app()->getRequestMethod())) {
            $result = false;
        }

        if ($result) {
            $successCallback && call_user_func($successCallback);
        } else {
            $failureCallback && call_user_func($failureCallback);
            Response::response_invalid_request(403);
            return false;
        }

        return parent::runAction($params, $successCallback, $failureCallback);
    }
}
