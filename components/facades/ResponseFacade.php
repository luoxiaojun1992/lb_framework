<?php

namespace lb\components\facades;

use lb\components\Response;

class ResponseFacade extends BaseFacade
{
    public static function getFacadeAccessor()
    {
        return Response::component();
    }
}
