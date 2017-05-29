<?php

namespace lb\components\token_bucket;

use lb\BaseClass;
use lb\components\response\ResponseContract;

class Writer extends BaseClass
{
    public static function write($content, \Closure $writeAction, ResponseContract $response)
    {
        Bucket::component(1000000, 1000000, 1000)->wait(strlen($content));
        call_user_func_array($writeAction, ['content' => $content, 'response' => $response]);
    }
}
