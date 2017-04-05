<?php

namespace lb\components\response;

use lb\BaseClass;
use lb\components\helpers\HttpHelper;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\components\traits\Singleton;
use lb\Lb;

class Response extends BaseClass implements ResponseContract
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
        $http_code = intval($http_code);
        $status_str = HttpHelper::get_status_code_message($http_code);
        if ($status_str) {
            header(implode(' ', [$protocol, $http_code, $status_str]));
        }
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
            Lb::app()->stop($response_content);
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
        return session_start();
    }

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return session_id();
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
        header($key . ':' . $value, $replace, $http_response_code);
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
        setcookie($cookie_key, Lb::app()->encrypt_by_config($cookie_value), $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Set session
     *
     * @param $sessionKey
     * @param $sessionValue
     */
    public function setSession($sessionKey, $sessionValue)
    {
        $_SESSION[$sessionKey] = $sessionValue;
    }

    /**
     * Delete session
     *
     * @param $sessionKey
     */
    public function delSession($sessionKey)
    {
        if (isset($_SESSION[$sessionKey])) {
            unset($_SESSION[$sessionKey]);
        }
    }
}
