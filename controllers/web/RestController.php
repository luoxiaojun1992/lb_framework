<?php

namespace lb\controllers\web;

use lb\components\middleware\AuthMiddleware;
use lb\components\middleware\RateLimitFilter;
use lb\components\middleware\RequestMethodFilter;
use lb\controllers\BaseController;
use lb\Lb;

class RestController extends BaseController
{
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
        if (Lb::app()->isRest()) {
            $restConfig = Lb::app()->getRest()[$this->controller_id][$this->action_id];
            list($requestMethod, $authType) = $restConfig;

            $response = $this->response;

            //Set Auth Middleware
            $this->middleware['authMiddleware']['params'] = [
                'rest_config' => $restConfig,
                'request' => $this->request,
            ];
            $this->middleware['authMiddleware']['failureCallback'] = function () use ($response) {
                $response->response_unauthorized(401);
            };

            //Set Request Method Filter
            $this->middleware['requestMethodFilter']['params'] = [
                'request_method' => $requestMethod,
                'request' => $this->request,
            ];
            $this->middleware['requestMethodFilter']['failureCallback'] = function () use ($response) {
                $response->response_invalid_request(403);
            };

            //Set Rate Limit Filter
            if (array_key_exists($this->action_id, $this->rateLimitActions)) {
                $this->middleware['rateLimitFilter']['params'] = $this->rateLimitActions[$this->action_id];
                $this->middleware['rateLimitFilter']['failureCallback'] = function () use ($response) {
                    $response->response_invalid_request(403);
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
        $this->response->response_invalid_request($status_code);
    }

    /**
     * Reponse Unauthorized Request
     *
     * @param int $status_code
     */
    protected function response_unauthorized($status_code = 200)
    {
        $this->beforeResponse();
        $this->response->response_unauthorized($status_code);
    }

    /**
     * Response Successful Request
     */
    protected function response_success()
    {
        $this->beforeResponse();
        $this->response->response_success();
    }

    /**
     * Response Failed Request
     *
     * @param int $status_code
     */
    protected function response_failed($status_code = 200)
    {
        $this->beforeResponse();
        $this->response->response_failed($status_code);
    }

    /**
     * Response
     *
     * @param $data
     * @param $format
     * @param bool $is_success
     * @param int $status_code
     */
    protected function response($data, $format, $is_success=true, $status_code = 200)
    {
        $this->beforeResponse();
        $this->response->response($data, $format, $is_success, $status_code);
    }
}
