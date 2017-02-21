<?php

namespace lb\components\facades;

use lb\components\cache\Filecache;

class FilecacheFacade extends BaseFacade
{
    public static function getFacadeAccessor()
    {
        return Filecache::component();
    }
}
