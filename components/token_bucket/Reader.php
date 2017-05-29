<?php

namespace lb\components\token_bucket;

use lb\BaseClass;
use lb\components\request\RequestContract;

class Reader extends BaseClass
{
    public static function read(\Closure $readAction, RequestContract $request)
    {
        $content = call_user_func_array($readAction, ['request' => $request]);
        if (($contentLength = strlen($content)) <= 0) {
            return $content;
        }
        Bucket::component(1000000, 1000000, 1000)->wait($contentLength);
        return $content;
    }
}
