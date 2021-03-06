<?php

namespace lb\components\response;

use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\components\session\SwooleSession;
use lb\components\traits\Singleton;
use lb\Lb;

class SwooleResponse extends ResponseAdapter implements ResponseContract
{
    use Singleton;

    /**
     * Send Http Code
     *
     * @param int $http_code
     * @param string $protocol
     */
    public function httpCode($http_code = 200, $protocol = 'HTTP/1.1')
    {
        $this->swooleResponse->status(intval($http_code));
    }

    /**
     * Response Request
     *
     * @param $data
     * @param $format
     * @param bool $is_success
     * @param int $status_code
     */
    public function response($data, $format, $is_success=true, $status_code = 200)
    {
        $this->httpCode($status_code);
        if ($is_success) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        switch ($format) {
            case self::RESPONSE_TYPE_JSON:
                $response_content = JsonHelper::encode($data);
                break;
            case self::RESPONSE_TYPE_XML:
                header('Content-type:application/xml');
                $response_content = XMLHelper::encode($data);
                break;
            default:
                $response_content = '';
        }
        if (!$is_success) {
            $this->swooleResponse->end($response_content);
        } else {
            echo $response_content;
        }
    }

    /**
     * Response Invalid Request
     *
     * @param int $status_code
     */
    public function response_invalid_request($status_code = 200)
    {
        $this->response(['msg' => 'invalid request'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    public function response_unauthorized($status_code = 200)
    {
        $this->response(['msg' => 'unauthorized'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Response Successful Request
     */
    public function response_success()
    {
        $this->response(['msg' => 'success'], static::RESPONSE_TYPE_JSON);
    }

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    public function response_failed($status_code = 200)
    {
        $this->response(['msg' => 'failed'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Start Session
     *
     * @return bool
     */
    public function startSession()
    {
        $swooleSession = SwooleSession::component();
        $swooleSession->gc(time());
        $swooleSession->write($this->getSessionId(), Lb::app()->serialize([]));
        return true;
    }

    /**
     * Set session
     *
     * @param $sessionKey
     * @param $sessionValue
     */
    public function setSession($sessionKey, $sessionValue)
    {
        $sessions = [];
        $swooleSession = SwooleSession::component();
        $swooleSession->gc(time());
        $sessionData =  $swooleSession->read($this->getSessionId());
        if ($sessionData) {
            $sessions = Lb::app()->unserialize($sessionData);
        }
        if (isset($sessions[$sessionKey])) {
            $sessions[$sessionKey] = $sessionValue;
            $swooleSession->write($this->getSessionId(), Lb::app()->serialize($sessions));
        }
    }

    /**
     * Delete session
     *
     * @param $sessionKey
     */
    public function delSession($sessionKey)
    {
        $sessions = [];
        $swooleSession = SwooleSession::component();
        $swooleSession->gc(time());
        $sessionData =  $swooleSession->read($this->getSessionId());
        if ($sessionData) {
            $sessions = Lb::app()->unserialize($sessionData);
        }
        if (isset($sessions[$sessionKey])) {
            unset($sessions[$sessionKey]);
            $swooleSession->write($this->getSessionId(), Lb::app()->serialize($sessions));
        }
    }

    /**
     * Set header
     *
     * @param $key
     * @param $value
     * @param bool $replace
     * @param $http_response_code
     */
    public function setHeader($key, $value, $replace = true, $http_response_code = null)
    {
        $this->swooleResponse->header($key. $value);
    }

    /**
     * Set cookie
     *
     * @param $cookie_key
     * @param $cookie_value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httpOnly
     */
    public function setCookie(
        $cookie_key,
        $cookie_value,
        $expire = null,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = null
    )
    {
        $this->swooleResponse->cookie(
            $cookie_key,
            Lb::app()->encryptByConfig($cookie_value),
            $expire,
            $path,
            $domain,
            $secure,
            $httpOnly
        );
    }
}
