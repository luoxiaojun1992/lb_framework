<?php

namespace lb\components\observers;

interface ObserverInterface
{
    public static function on($event_name, $listener, $data = null);

    public static function trigger($event_name, $event = null);
}
