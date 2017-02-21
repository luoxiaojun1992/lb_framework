<?php

namespace lb\components\facades;

use lb\components\cache\Filecache;

class FilecacheFacade extends BaseFacade
{
    const CACHE_TYPE = Filecache::CACHE_TYPE;

    public static function getFacadeAccessor()
    {
        return Filecache::component();
    }
}
