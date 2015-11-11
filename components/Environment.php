<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/11
 * Time: 16:25
 * Lb framework environment component file
 */

namespace lb\components;

class Environment
{
    public static function getValue($env_name)
    {
        return getenv($env_name);
    }
}
