<?php

namespace lb\controllers\web;

use lb\components\Auth;
use lb\components\middleware\AuthMiddleware;
use lb\components\middleware\RateLimitFilter;
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
        'rateLimitFilter' => [
            'class' => RateLimitFilter::class,
        ],
    ];

    //Rate Limit Actions
    protected $rateLimitActions = [
        //Example Rate Limit Configuration
//        'index' => [
//            'rate' => 60,
//            'expire' => 60,
//            'step' => 1,
//            'key' => self::class . '@' . 'index',
//        ]
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

            //Set Auth Middleware
            $this->middleware['authMiddleware']['params'] = [
                'auth_type' => $this->auth_type,
                'rest_config' => $this->self_rest_config,
            ];

            //Set Request Method Filter
            $this->middleware['requestMethodFilter']['params'] = [
                'request_method' => $this->request_method,
            ];

            //Set Rate Limit Filter
            if (array_key_exists($route_info['action'], $this->rateLimitActions)) {
                $this->middleware['rateLimitFilter']['params'] = $this->rateLimitActions[$route_info['action']];
                $this->middleware['rateLimitFilter']['failureCallback'] = function () {
                    Response::response_invalid_request(403);
                };
            }
        } else {
            $this->response_invalid_request(403);
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
