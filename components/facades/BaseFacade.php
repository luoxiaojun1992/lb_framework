<?php

namespace lb\components\facades;

abstract class BaseFacade
{
    public static function getFacadeAccessor()
    {
        return new \stdClass();
    }

    public static function __callStatic($method, $args)
    {
        $accessor = static::getFacadeAccessor();
        if (method_exists($accessor, $method)) {
            return call_user_func_array([$accessor, $method], $args);
        }

        return null;
    }
}
