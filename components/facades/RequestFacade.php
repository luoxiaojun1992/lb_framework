<?php

namespace lb\components\facades;

use lb\components\Request;

class RequestFacade extends BaseFacade
{
    public static function getFacadeAccessor()
    {
        return Request::component();
    }
}
