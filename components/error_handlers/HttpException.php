<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/10
 * Time: 13:38
 * Lb framework http exception component file
 */

namespace lb\components\error_handlers;

use lb\Lb;

class HttpException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Lb::app()->stop(implode(':', [$code, $message]));
    }
}
