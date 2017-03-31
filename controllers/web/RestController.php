<?php

namespace lb\controllers\web;

use lb\components\Auth;
use lb\components\middleware\AuthMiddleware;
use lb\components\middleware\RequestMethodFilter;
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
        ],
        'requestMethodFilter' => [
            'class' => RequestMethodFilter::class,
        ],
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

            $this->middleware['requestMethodFilter']['params'] = [
                'request_method' => $this->request_method,
            ];
        } else {
            $this->response_invalid_request();
        }

        parent::beforeAction();
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
        $this->beforeResponse();
        Response::response_invalid_request($status_code);
    }

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    protected function response_unauthorized($status_code = 200)
    {
        $this->beforeResponse();
        Response::response_unauthorized($status_code);
    }

    /**
     * Response Successful Request
     */
    protected function response_success()
    {
        $this->beforeResponse();
        Response::response_success();
    }

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    protected function response_failed($status_code = 200)
    {
        $this->beforeResponse();
        Response::response_failed($status_code);
    }
}
