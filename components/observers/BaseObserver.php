<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/08/08
 * Time: 9:38
 * Lb framework observer component file
 */

namespace lb\components\observers;

use lb\components\events\BaseEvent;
use lb\components\listeners\BaseListener;

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
                $listener = $event_listener[1];
                $data = $event_listener[2];
                if (!$event) {
                    $event = new BaseEvent();
                }
                if ($data) {
                    $event->data = $data;
                }
                $listener->handler($event);
            }
        }
    }
}
