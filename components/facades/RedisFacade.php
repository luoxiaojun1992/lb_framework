<?php

namespace lb\components\facades;

use lb\components\cache\Redis;

class RedisFacade extends BaseFacade
{
    const CACHE_TYPE = Redis::CACHE_TYPE;

    public static function getFacadeAccessor()
    {
        return Redis::component();
    }
}
