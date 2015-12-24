<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/24
 * Time: 10:07
 * Lb framework system helper component file
 */

namespace lb\components\helpers;

use lb\Lb;

class SystemHelper
{
    public static function getVersion()
    {
        if (isset(Lb::app()->containers['config'])) {
            return Lb::app()->containers['config']->get('version') ? : Lb::VERSION;
        }
        return Lb::VERSION;
    }
}
