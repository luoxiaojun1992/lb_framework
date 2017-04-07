<?php

namespace lb\components\traits\lb;

use RequestKit;
use ResponseKit;

trait Cookie
{
    /**
     * Get Cookie Value
     *
     * @param $cookie_key
     * @return bool|mixed|string
     */
    public function getCookie($cookie_key)
    {
        if ($this->isSingle()) {
            return RequestKit::getCookie($cookie_key);
        }
        return false;
    }

    /**
     * Set Cookie Value
     *
     * @param $cookie_key
     * @param $cookie_value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httpOnly
     */
    public function setCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if ($this->isSingle()) {
            ResponseKit::setCookie($cookie_key, $cookie_value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    /**
     * Delete Cookie
     *
     * @param $cookie_key
     */
    public function delCookie($cookie_key)
    {
        if ($this->isSingle()) {
            if ($this->getCookie($cookie_key)) {
                $this->setCookie($cookie_key, null);
            }
        }
    }

    /**
     * Delete Multi Cookies
     *
     * @param $cookie_keys
     */
    public function delCookies($cookie_keys)
    {
        if ($this->isSingle()) {
            foreach ($cookie_keys as $cookie_key) {
                $this->delCookie($cookie_key);
            }
        }
    }
}
