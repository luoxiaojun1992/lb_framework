<?php

namespace lb\components\facades;

use lb\components\cache\Memcache;

class MemcacheFacade extends BaseFacade
{
    const CACHE_TYPE = Memcache::CACHE_TYPE;

    public static function getFacadeAccessor()
    {
        return Memcache::component();
    }
}
