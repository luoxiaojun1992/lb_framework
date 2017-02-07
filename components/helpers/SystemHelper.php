<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\Lb;

class SystemHelper extends BaseClass
{
    public static function getVersion()
    {
        return Lb::app()->getConfigByName('version') ? : Lb::VERSION;
    }
}
