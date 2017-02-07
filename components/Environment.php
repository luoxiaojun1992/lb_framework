<?php

namespace lb\components;

use lb\BaseClass;

class Environment extends BaseClass
{
    public static function getValue($env_name)
    {
        return getenv($env_name);
    }
}
