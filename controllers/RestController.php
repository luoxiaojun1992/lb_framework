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

class RestController extends BaseController
{
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_XML = 2;

    protected function beforeAction()
    {
        parent::beforeAction();

        $this->authentication();
    }

    protected function authentication()
    {

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
    }
}
