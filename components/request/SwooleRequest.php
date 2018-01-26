<?php

namespace lb\components\request;

use lb\components\containers\Header;
use lb\components\helpers\EncodeHelper;
use lb\components\session\SwooleSession;
use lb\components\traits\Singleton;
use lb\Lb;

class SwooleRequest extends RequestAdapter
{
    use Singleton;

    /** @var  Header */
    protected $_headers;

    public function getClientAddress()
    {
        $swooleRequest = $this->swooleRequest;
        $header = $swooleRequest->header;
        $ip = false;
        if (!empty($header["client-ip"])) {
            $ip = $header["client-ip"];
        }
        if (!empty($header['x-forwarded-for'])) {
            $ips = explode (", ", $header['x-forwarded-for']);
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
        return ($ip ? $ip : $swooleRequest->server['remote_addr']);
    }

    public function getHost()
    {
        $swooleRequest = $this->swooleRequest;
        $header = $swooleRequest->header;
        return isset($header['x-forwarded-host']) ? $header['x-forwarded-host'] :
            (isset($header['host']) ? $header['host'] : '');
    }

    public function getUri()
    {
        $swooleRequest = $this->swooleRequest;
        $server = $swooleRequest->server;
        return isset($server['request_uri']) ? $server['request_uri'] : '';
    }

    public function getHostAddress()
    {
        $swooleRequest = $this->swooleRequest;
        $server = $swooleRequest->server;
        return $server['server_addr'] ?? gethostbyname(gethostname());
    }

    public function getUserAgent()
    {
        $swooleRequest = $this->swooleRequest;
        $header = $swooleRequest->header;
        return isset($header['user-agent']) ? $header['user-agent'] : '';
    }

    public function getRequestMethod()
    {
        $swooleRequest = $this->swooleRequest;
        $server = $swooleRequest->server;
        return isset($server['request_method']) ? $server['request_method'] : '';
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
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function getBasicAuthUser()
    {
        list($authType, $authCode) = explode(' ', $this->swooleRequest->header['authorization']);
        if ($authType == 'Basic') {
            list($user, $pw) = explode(':', EncodeHelper::base64Decode($authCode));
            return $user;
        }
        return '';
    }

    public function getBasicAuthPassword()
    {
        list($authType, $authCode) = explode(' ', $this->swooleRequest->header['authorization']);
        if ($authType == 'Basic') {
            list($user, $pw) = explode(':', EncodeHelper::base64Decode($authCode));
            return $pw;
        }
        return '';
    }

    public function getHeaders() : Header
    {
        if ($this->_headers === null) {
            $this->_headers = new Header();
            foreach ($this->swooleRequest->header as $name => $value) {
                $this->_headers->set(strtoupper($name), $value);
            }
        }

        return $this->_headers;
    }

    public function getParam($param_name, $default_value = null)
    {
        if (isset($this->swooleRequest->get[$param_name])) {
            return $this->swooleRequest->get[$param_name];
        }

        if (isset($this->swooleRequest->post[$param_name])) {
            return $this->swooleRequest->post[$param_name];
        }

        return $default_value;
    }

    public function getQueryParams()
    {
        return $this->swooleRequest->get;
    }

    public function getBodyParams()
    {
        return $this->swooleRequest->post;
    }

    public function getRawContent()
    {
        return $this->swooleRequest->rawContent();
    }

    public function getCookie($cookie_key)
    {
        $cookie = $this->swooleRequest->cookie;
        return isset($cookie[$cookie_key]) ?
            Lb::app()->decryptByConfig($cookie[$cookie_key]) : false;
    }

    public function getFile($file_name)
    {
        $files = $this->swooleRequest->files;
        return isset($files[$file_name]) ? $files[$file_name] : false;
    }

    public function getSession($session_key)
    {
        $sessions = [];
        $swooleSession = SwooleSession::component();
        $swooleSession->gc(time());
        $sessionData =  $swooleSession->read($this->getSessionId());
        if ($sessionData) {
            $sessions = Lb::app()->unserialize($sessionData);
        }
        return isset($sessions[$session_key]) ? $sessions[$session_key] : false;
    }
}
