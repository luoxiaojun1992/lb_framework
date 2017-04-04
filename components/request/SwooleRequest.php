<?php

namespace lb\components\request;

use lb\components\adapters\RequestAdapter;
use lb\components\containers\Header;
use lb\components\contracts\RequestContract;
use lb\components\session\MysqlSession;
use lb\components\traits\Singleton;
use lb\Lb;

class SwooleRequest extends RequestAdapter implements RequestContract
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
        return isset($server['server_addr']) ? $server['server_addr'] : '';
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
            list($user, $pw) = explode(':', base64_decode($authCode));
            return $user;
        }
        return '';
    }

    public function getBasicAuthPassword()
    {
        list($authType, $authCode) = explode(' ', $this->swooleRequest->header['authorization']);
        if ($authType == 'Basic') {
            list($user, $pw) = explode(':', base64_decode($authCode));
            return $pw;
        }
        return '';
    }

    public function getHeaders() : Header
    {
        if ($this->_headers === null) {
            $this->_headers = Header::component();
            foreach ($this->swooleRequest->header as $name => $value) {
                $this->_headers->set(strtoupper($name), $value);
            }
        }

        return $this->_headers;
    }

    public function getHeader($headerKey)
    {
        return $this->getHeaders()->get($headerKey);
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

    public function getRawContent()
    {
        return $this->swooleRequest->rawContent();
    }

    public function getCookie($cookie_key)
    {
        $cookie = $this->swooleRequest->cookie;
        return isset($cookie[$cookie_key]) ?
            Lb::app()->decrypt_by_config($cookie[$cookie_key]) : false;
    }

    public function getFile($file_name)
    {
        $files = $this->swooleRequest->files;
        return isset($files[$file_name]) ? $files[$file_name] : false;
    }

    public function getSession($session_key)
    {
        $sessions = [];
        //todo handle the duplicate of php session
        $mysqlSession = MysqlSession::component();
        $mysqlSession->gc(time());
        $sessionData =  $mysqlSession->read($this->getSessionId());
        if ($sessionData) {
            $sessions = unserialize($sessionData);
        }
        return isset($sessions[$session_key]) ? $sessions[$session_key] : false;
    }

    public function getSessionId()
    {
        return $this->getCookie('swoole_session_id');
    }
}
