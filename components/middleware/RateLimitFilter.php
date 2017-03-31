<?php

namespace lb\components\middleware;

use lb\components\traits\RateLimit;

class RateLimitFilter extends BaseMiddleware
{
    use RateLimit;

    public function runAction($params, $successCallback, $failureCallback)
    {
        if ($params) {
            if ($result = !$this->isOverRate($params['rate'], $params['key'])) {
                $this->setRate($params['expire'], $params['step'], $params['key']);
                $successCallback && call_user_func($successCallback);
            } else {
                $failureCallback && call_user_func($failureCallback);
                return false;
            }
        }

        return parent::runAction($params, $successCallback, $failureCallback);
    }
}
