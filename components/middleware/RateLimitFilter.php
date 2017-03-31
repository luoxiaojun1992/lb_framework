<?php

namespace lb\components\middleware;

class RateLimitFilter extends BaseMiddleware
{
    public function runAction($params, $successCallback, $failureCallback)
    {
        $result = true;

        if ($result) {
            $successCallback && call_user_func($successCallback);
        } else {
            $failureCallback && call_user_func($failureCallback);
            return false;
        }

        return parent::runAction($params, $successCallback, $failureCallback);
    }
}
