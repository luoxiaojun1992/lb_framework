<?php

namespace lb\components\facades;

use lb\components\cache\Memcache;

class MemcacheFacade extends BaseFacade
{
    public static function getFacadeAccessor()
    {
        return Memcache::component();
    }
}
