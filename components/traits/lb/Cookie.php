<?php

namespace lb\components\traits\lb;

use RequestKit;
use ResponseKit;

trait Cookie
{
    // Get Cookie Value
    public function getCookie($cookie_key)
    {
        if ($this->isSingle()) {
            return RequestKit::getCookie($cookie_key);
        }
        return false;
    }

    // Set Cookie Value
    public function setCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if ($this->isSingle()) {
            ResponseKit::setCookie($cookie_key, $cookie_value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    // Delete Cookie
    public function delCookie($cookie_key)
    {
        if ($this->isSingle()) {
            if ($this->getCookie($cookie_key)) {
                $this->setCookie($cookie_key, null);
            }
        }
    }

    // Delete Multi Cookies
    public function delCookies($cookie_keys)
    {
        if ($this->isSingle()) {
            foreach ($cookie_keys as $cookie_key) {
                $this->delCookie($cookie_key);
            }
        }
    }
}
