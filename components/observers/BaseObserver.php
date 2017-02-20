<?php

namespace lb\components\observers;

use lb\components\events\BaseEvent;
use lb\components\listeners\BaseListener;
use lb\components\listeners\ListenerInterface;

class BaseObserver implements ObserverInterface
{
    protected static $event_listeners = [];

    public static function on($event_name, BaseListener $listener, $data = null)
    {
        static::$event_listeners[] = [$event_name, $listener, $data];
    }

    public static function trigger($event_name, $event = null)
    {
        foreach(static::$event_listeners as $event_listener) {
            if ($event_listener[0] == $event_name) {
                $event = $event ? : new BaseEvent();
                if ($data = $event_listener[2]) {
                    $event->data = $data;
                }

                /** @var ListenerInterface $listener */
                $listener = $event_listener[1];
                $listener->handler($event);
            }
        }
    }
}
