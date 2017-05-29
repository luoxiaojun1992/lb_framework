<?php

namespace lb\components\token_bucket;

use lb\components\request\RequestContract;

class Reader
{
    public static function read(\Closure $readAction, RequestContract $request)
    {
        $content = call_user_func_array($readAction, ['request' => $request]);
        if (($contentLength = strlen($content)) <= 0) {
            return $content;
        }
        Bucket::component()->wait($contentLength);
        return $content;
    }
}
