<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/08/08
 * Time: 9:38
 * Lb framework observer component file
 */

namespace lb\components\observers;

use lb\components\listeners\Base;
use lb\components\events\Base;

class Observer implements ObserverInterface
{
    protected static $event_listeners = [];

    public static function on($event_name, Base $listener, $data = null)
    {
        static::$event_listeners[] = [$event_name, $listener, $da];
    }

    public static function trigger($event_name, Base $event);
    {

    }
}
