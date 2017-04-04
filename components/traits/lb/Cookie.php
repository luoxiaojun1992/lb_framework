<?php

namespace lb\components\traits\lb;

use RequestKit;

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
            $cookie_value = $this->encrypt_by_config($cookie_value);
            setcookie($cookie_key, $cookie_value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    // Set Cookie Value By Header
    public function setHeaderCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if ($this->isSingle()) {
            $cookie_value = $this->encrypt_by_config($cookie_value);
            $cookie_str[] = $cookie_key . '=' . $cookie_value;
            if ($expire) {
                $cookie_str[] = 'expires=' . gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT", time() + $expire);
            }
            if ($path) {
                $cookie_str[] = 'path=' . $path;
            }
            if ($domain) {
                $cookie_str[] = 'domain=' . $domain;
            }
            if ($secure) {
                $cookie_str[] = 'secure';
            }
            if ($httpOnly) {
                $cookie_str[] = 'HttpOnly';
            }
            header("Set-Cookie: " . implode('; ', $cookie_str), false);
        }
    }

    // Delete Cookie
    public function delCookie($cookie_key)
    {
        if ($this->isSingle()) {
            if (isset($_COOKIE[$cookie_key])) {
                setcookie($cookie_key);
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

    // Delete Cookie By Header
    public function delHeaderCookie($cookie_key)
    {
        if ($this->isSingle()) {
            if (isset($_COOKIE[$cookie_key])) {
                $this->setHeaderCookie($cookie_key, $_COOKIE[$cookie_key], -1);
            }
        }
    }

    // Delete Multi Cookies By Header
    public function delHeaderCookies($cookie_keys)
    {
        if ($this->isSingle()) {
            foreach ($cookie_keys as $cookie_key) {
                $this->delHeaderCookie($cookie_key);
            }
        }
    }
}
