<?php

namespace lb\components\response;

use lb\BaseClass;
use lb\components\helpers\HttpHelper;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\components\traits\Singleton;
use lb\Lb;

class Response extends BaseClass
{
    use Singleton;

    // Response Type
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_XML = 2;

    protected $swooleResponse;

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
        echo $response_content;
        if (!$is_success) {
            Lb::app()->stop();
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
     * Set swoole response
     *
     * @param $swooleResponse
     * @return $this
     */
    public function setSwooleResponse($swooleResponse)
    {
        $this->swooleResponse = $swooleResponse;
        return $this;
    }
}
