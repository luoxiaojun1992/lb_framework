<?php

namespace lb\components\observers;

use lb\components\listeners\BaseListener;

interface ObserverInterface
{
    public static function on($event_name, BaseListener $listener, $data = null);

    public static function trigger($event_name, $event = null);
}
