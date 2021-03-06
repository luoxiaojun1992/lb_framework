<?php

namespace lb\components\request;

use lb\components\containers\Header;
use lb\components\traits\Singleton;
use lb\Lb;

class Request extends BaseRequest
{
    use Singleton;

    /**
     * @var  Header 
     */
    protected $_headers;

    public function getClientAddress()
    {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
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
        return $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
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
            $this->_headers = new Header();
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

    public function getParam($param_name, $default_value = null)
    {
        return isset($_REQUEST[$param_name]) ? $_REQUEST[$param_name] : $default_value;
    }

    public function getRawContent()
    {
        return file_get_contents('php://input');
    }

    public function getCookie($cookie_key)
    {
        return isset($_COOKIE[$cookie_key]) ?
            Lb::app()->decryptByConfig($_COOKIE[$cookie_key]) : false;
    }

    public function getFile($file_name)
    {
        return isset($_FILES[$file_name]) ? $_FILES[$file_name] : false;
    }

    public function getSession($session_key)
    {
        return isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : false;
    }

    public function getSessionId()
    {
        return session_id();
    }

    public function getQueryParams()
    {
        return $_GET;
    }

    public function getBodyParams()
    {
        return $_POST;
    }
}
