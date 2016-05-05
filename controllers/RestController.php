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

    protected function response($data, $format)
    {
        switch ($format) {
            case 'json':
                $response_content = JsonHelper::encode($data);
                break;
            case 'xml':
                $response_content = XMLHelper::encode($data);
                break;
            default:
                $response_content = '';
        }
        echo $response_content;
    }
}
