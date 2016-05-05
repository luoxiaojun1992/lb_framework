<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 15:33
 * Lb framework rest controller file
 */

namespace lb\controllers;

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

    public $auth_type = 1;
    protected $rest_config = [];
    protected $self_rest_config = [];

    protected function beforeAction()
    {
        parent::beforeAction();

        $this->rest_config = Lb::app()->getRest();
        $this->validRequestMethod();
        $this->authentication();
    }

    protected function validRequestMethod()
    {
        $route_info = Lb::app()->getRouteInfo();
        if (isset($this->rest_config[$route_info['controller']][$route_info['action']])) {
            $this->self_rest_config = $this->rest_config[$route_info['controller']][$route_info['action']];
            list($request_method, $this->auth_type) = $this->self_rest_config;
            if (strtolower($request_method) != strtolower(Lb::app()->getRequestMethod())) {
                $this->response(['msg' => 'invalid request'], static::RESPONSE_TYPE_JSON, false);
            }
        } else {
            $this->response(['msg' => 'invalid request'], static::RESPONSE_TYPE_JSON, false);
        }
    }

    protected function authentication()
    {
        switch($this->auth_type) {
            case 1:
                $auth_user = Lb::app()->getBasicAuthUser();
                $auth_pwd = Lb::app()->getBasicAuthPassword();
                if ($auth_user != $this->self_rest_config[2][0] || md5($auth_pwd) != $this->self_rest_config[2][1]) {
                    $this->response(['msg' => 'unauthorized'], static::RESPONSE_TYPE_JSON, false);
                }
                break;
            case 2:
                break;
            default:
        }
    }

    protected function beforeResponse()
    {

    }

    protected function response($data, $format, $is_success=true)
    {
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
