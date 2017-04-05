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
     */
    public function runAction($params, $successCallback, $failureCallback)
    {
        $result = true;
        /** @var RequestContract $request */
        $request = $params['request'];
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
