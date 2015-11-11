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
use lb\Lb;

class RestController extends BaseController
{
    protected function beforeResponse()
    {

    }

    protected function response($data, $format)
    {
        $response_content = '';
        switch ($format) {
            case 'json' :
                $response_content = JsonHelper::encode($data);
                break;
        }
        Lb::app()->stop($response_content);
    }
}
