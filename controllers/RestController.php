<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 15:33
 * Lb framework rest controller file
 */

namespace lb\controllers;

use lb\components\Response;
use lb\components\helpers\JsonHelper;
use lb\components\helpers\XMLHelper;
use lb\Lb;

class RestController extends BaseController
{
    // Response Type
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_XML = 2;

    // Authentication Type
    const AUTH_TYPE_BASIC = 1;
    const AUTH_TYPE_OAUTH = 2;
    const AUTH_TYPE_QUERY_STRING = 3;

    protected $auth_type = 1;
    protected $request_method = '';
    protected $rest_config = [];
    protected $self_rest_config = [];

    protected function beforeAction()
    {
        parent::beforeAction();

        $this->rest_config = Lb::app()->getRest();
        $route_info = Lb::app()->getRouteInfo();
        if (isset($this->rest_config[$route_info['controller']][$route_info['action']])) {
            $this->self_rest_config = $this->rest_config[$route_info['controller']][$route_info['action']];
            list($this->request_method, $this->auth_type) = $this->self_rest_config;
        } else {
            $this->response_invalid_request();
        }

        $this->validRequestMethod();
        $this->authentication();
    }

    protected function validRequestMethod()
    {
        $this->request_method = trim($this->request_method);
        if ($this->request_method != '*' && strtolower($this->request_method) != strtolower(Lb::app()->getRequestMethod())) {
            $this->response_invalid_request();
        }
    }

    protected function authentication()
    {
        switch($this->auth_type) {
            case 1:
                $auth_user = Lb::app()->getBasicAuthUser();
                $auth_pwd = Lb::app()->getBasicAuthPassword();
                if ($auth_user != $this->self_rest_config[2][0] || md5($auth_pwd) != $this->self_rest_config[2][1]) {
                    $this->response_unauthorized();
                }
                break;
            case 2:
                break;
            case 3:
                $auth_key = $this->self_rest_config[2][0];
                $auth_value = Lb::app()->getParam($auth_key);
                if (md5($auth_value) != $this->self_rest_config[2][1]) {
                    $this->response_unauthorized();
                }
                break;
            default:
        }
    }

    protected function beforeResponse()
    {

    }

    protected function response_invalid_request($status_code = 200)
    {
        $this->response(['msg' => 'invalid request'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    protected function response_unauthorized($status_code = 200)
    {
        $this->response(['msg' => 'unauthorized'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    protected function response_success()
    {
        $this->response(['msg' => 'success'], static::RESPONSE_TYPE_JSON);
    }

    protected function response_failed($status_code = 200)
    {
        $this->response(['msg' => 'failed'], static::RESPONSE_TYPE_JSON, false, $status_code);
    }

    protected function response($data, $format, $is_success=true, $status_code = 200)
    {
        Response::httpCode($status_code);
        if ($is_success) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        switch ($format) {
            // Response JSON
            case 1:
                $response_content = JsonHelper::encode($data);
                break;
            // Response XML
            case 2:
                Header('Content-type:application/xml');
                $response_content = XMLHelper::encode($data);
                break;
            default:
                $response_content = '';
        }
        echo $response_content;
        if (!$is_success) {
            die();
        }
    }
}
