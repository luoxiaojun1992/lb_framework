<?php

namespace lb\components\response;

interface ResponseContract
{
    // Response Type
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_XML = 2;

    /**
     * Send Http Code
     *
     * @param int $http_code
     * @param string $protocol
     */
    public function httpCode($http_code = 200, $protocol = 'HTTP/1.1');

    /**
     * Response Request
     *
     * @param $data
     * @param $format
     * @param bool $is_success
     * @param int $status_code
     */
    public function response($data, $format, $is_success=true, $status_code = 200);

    /**
     * Response Invalid Request
     *
     * @param int $status_code
     */
    public function response_invalid_request($status_code = 200);

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    public function response_unauthorized($status_code = 200);

    /**
     * Response Successful Request
     */
    public function response_success();

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    public function response_failed($status_code = 200);

    /**
     * Start Session
     *
     * @return bool
     */
    public function startSession();

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Set header
     *
     * @param $key
     * @param $value
     * @param bool $replace
     */
    public function setHeader($key, $value, $replace = true);

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
    );

    /**
     * Set session
     *
     * @param $sessionKey
     * @param $sessionValue
     */
    public function setSession($sessionKey, $sessionValue);

    /**
     * Delete session
     *
     * @param $sessionKey
     */
    public function delSession($sessionKey);
}
