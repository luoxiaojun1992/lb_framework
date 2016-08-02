<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/24
 * Time: 10:07
 * Lb framework system helper component file
 */

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
