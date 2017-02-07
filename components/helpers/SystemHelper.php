<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\components\consts\Info;
use lb\Lb;

class SystemHelper extends BaseClass implements Info
{
    public static function getVersion()
    {
        return Lb::app()->getConfigByName('version') ? : self::VERSION;
    }
}
