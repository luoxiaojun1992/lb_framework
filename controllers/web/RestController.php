<?php

namespace lb\controllers\web;

use lb\components\Auth;
use lb\components\middleware\AuthMiddleware;
use lb\components\Response;
use lb\controllers\BaseController;
use lb\Lb;

class RestController extends BaseController
{
    protected $auth_type = Auth::AUTH_TYPE_BASIC;
    protected $request_method = '';
    protected $rest_config = [];
    protected $self_rest_config = [];

    //Middleware
    protected $middleware = [
        'authMiddleware' => [
            'class' => AuthMiddleware::class,
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
        Response::response_invalid_request($status_code);
    }

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    protected function response_unauthorized($status_code = 200)
    {
        Response::response_unauthorized($status_code);
    }

    /**
     * Response Successful Request
     */
    protected function response_success()
    {
        Response::response_success();
    }

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    protected function response_failed($status_code = 200)
    {
        Response::response_failed($status_code);
    }
}
