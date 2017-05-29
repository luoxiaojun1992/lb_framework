<?php

namespace lb\components\token_bucket;

use lb\components\response\ResponseContract;

class Writer
{
    public static function write($content, \Closure $writeAction, ResponseContract $response)
    {
        Bucket::component()->wait(strlen($content));
        call_user_func_array($writeAction, ['content' => $content, 'response' => $response]);
    }
}
