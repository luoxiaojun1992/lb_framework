<?php

namespace lb\controllers\web;

use lb\components\Auth;
use lb\components\middleware\AuthMiddleware;
use lb\components\Response;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\controllers\BaseController;
use lb\Lb;

class RestController extends BaseController
{
    // Response Type
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_XML = 2;

    protected $auth_type = Auth::AUTH_TYPE_BASIC;
    protected $request_method = '';
    protected $rest_config = [];
    protected $self_rest_config = [];

    //Middleware
    protected $middleware = [
        'authMiddleware' => [
            'class' => AuthMiddleware::class,
            'params' => [],
            'action' => '',
            'successCallback' => null,
            'failureCallback' => null,
        ]
    ];

    /**
     * Before Action Filter
     */
    protected function beforeAction()
    {
        $this->rest_config = Lb::app()->getRest();
        $route_info = Lb::app()->getRouteInfo();
        if (isset($this->rest_config[$route_info['controller']][$route_info['action']])) {
            $this->self_rest_config = $this->rest_config[$route_info['controller']][$route_info['action']];
            list($this->request_method, $this->auth_type) = $this->self_rest_config;

            $this->middleware['authMiddleware']['params'] = [
                'auth_type' => $this->auth_type,
                'rest_config' => $this->self_rest_config,
            ];
            $this->middleware['authMiddleware']['failureCallback'] = function () use ($this) {
                $this->response_unauthorized(401);
            };
        } else {
            $this->response_invalid_request();
        }

        $this->validRequestMethod();
    }

    /**
     * Valid Request Method
     */
    protected function validRequestMethod()
    {
        $this->request_method = trim($this->request_method);
        if ($this->request_method != '*' &&
            strtolower($this->request_method) != strtolower(Lb::app()->getRequestMethod())) {
            $this->response_invalid_request();
        }
    }

    /**
     * Before Response Filter
     */
    protected function beforeResponse()
    {
        //
    }

    /**
     * Response Invalid Request
     *
     * @param int $status_code
     */
    protected function response_invalid_request($status_code = 200)
    {
        $this->response(['msg' => 'invalid request'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    protected function response_unauthorized($status_code = 200)
    {
        $this->response(['msg' => 'unauthorized'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Response Successful Request
     */
    protected function response_success()
    {
        $this->response(['msg' => 'success'], static::RESPONSE_TYPE_JSON);
    }

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    protected function response_failed($status_code = 200)
    {
        $this->response(['msg' => 'failed'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    /**
     * Response Request
     *
     * @param $data
     * @param $format
     * @param bool $is_success
     * @param int $status_code
     */
    protected function response($data, $format, $is_success=true, $status_code = 200)
    {
        Response::httpCode($status_code);
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
                Header('Content-type:application/xml');
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
}
