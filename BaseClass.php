<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/1/2
 * Time: 下午4:25
 * Lb framework base class file
 */

namespace lb;

use lb\components\consts\ErrorMsg;

class BaseClass implements ErrorMsg
{
    public static function className()
    {
        return get_called_class();
    }
}
