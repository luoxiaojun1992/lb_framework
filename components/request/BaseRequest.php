<?php

namespace lb\components\request;

use lb\BaseClass;
use lb\components\containers\Cookie;
use lb\components\containers\Header;
use lb\Lb;

abstract class BaseRequest extends BaseClass implements RequestContract
{
    abstract public function getHeaders() : Header;

    abstract public function getCookies() : Cookie;

    public function getCookie($cookie_key)
    {
        $cookie = $this->getCookies()->get($cookie_key);

        return $cookie ? Lb::app()->decrypt_by_config($cookie) : $cookie;
    }

    public function getHeader($headerKey)
    {
        return $this->getHeaders()->get($headerKey);
    }
}
