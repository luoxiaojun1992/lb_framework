<?php

namespace lb\components\facades;

use lb\components\cache\Redis;

class RedisFacade extends BaseFacade
{
    public static function getFacadeAccessor()
    {
        return Redis::component();
    }
}
