<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\helpers\HttpHelper;

class Response extends BaseClass
{
    public static function httpCode($http_code = 200, $protocol = 'HTTP/1.1')
    {
        $http_code = intval($http_code);
        $status_str = HttpHelper::get_status_code_message($http_code);
        if ($status_str) {
            header(implode(' ', [$protocol, $http_code, $status_str]));
        }
    }
}
