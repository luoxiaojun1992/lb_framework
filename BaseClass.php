<?php

namespace lb;

use lb\components\consts\ErrorMsg;

class BaseClass implements ErrorMsg
{
    public static function className()
    {
        return get_called_class();
    }
}
