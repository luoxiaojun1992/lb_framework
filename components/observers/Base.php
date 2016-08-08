<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/08/08
 * Time: 9:38
 * Lb framework observer component file
 */

namespace lb\components\observers;

class Base implements ObserverInterface
{
    protected static $event_listeners = [];

    public static function on($event_name, lb\components\listeners\Base $listener, $data = null)
    {
        static::$event_listeners[] = [$event_name, $listener, $data];
    }

    public static function trigger($event_name, $event = null);
    {
        foreach(static::$event_listeners as $event_listener) {
            if ($event_listener[0] == $event_name) {
                $listener = $event_name[1];
                $data = $event_name[2];
                if (!$event) {
                    $event = new lb\components\events\Base();
                }
                if ($data) {
                    $event->data = $data;
                }
                $listener->handler($event);
            }
        }
    }
}
