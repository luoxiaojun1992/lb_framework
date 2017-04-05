<?php

namespace lb\components\middleware;

use lb\components\request\RequestContract;
use lb\Lb;

class RequestMethodFilter extends BaseMiddleware
{
    /**
     * @param $params
     * @param $successCallback
     * @param $failureCallback
     * @param RequestContract $request
     */
    public function runAction($params, $successCallback, $failureCallback, $request = null)
    {
        $result = true;
        $requestMethod = trim($params['request_method']);
        if ($requestMethod != '*' &&
            strtolower($requestMethod) != strtolower($request ? $request->getRequestMethod() :
                Lb::app()->getRequestMethod())) {
            $result = false;
        }

        if ($result) {
            $successCallback && call_user_func($successCallback);
        } else {
            $failureCallback && call_user_func($failureCallback);
        }

        $this->runNextMiddleware();
    }
}
