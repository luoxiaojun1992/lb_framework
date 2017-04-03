<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\containers\Header;
use lb\components\traits\Singleton;

class Request extends BaseClass
{
    use Singleton;

    /** @var  Header */
    protected $_headers;

    protected $swooleRequest;

    public function getClientAddress()
    {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match("/^(10│172.16│192.168)./", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    public function getHost()
    {
        return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    }

    public function getUri()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    public function getHostAddress()
    {
        return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    }

    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public function getRequestMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    }

    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    public function getReferer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function getBasicAuthUser()
    {
        return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
    }

    public function getBasicAuthPassword()
    {
        return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
    }

    public function getHeaders() : Header
    {
        if ($this->_headers === null) {
            $this->_headers = Header::component();
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->_headers->set($name, $value);
                    }
                }

                return $this->_headers;
            }
            foreach ($headers as $name => $value) {
                $this->_headers->set($name, $value);
            }
        }

        return $this->_headers;
    }

    public function getHeader($headerKey)
    {
        return $this->getHeaders()->get($headerKey);
    }

    public function setSwooleRequest($swooleRequest)
    {
        $this->swooleRequest = $swooleRequest;
        return $this;
    }
}
