<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/5
 * Time: 14:26
 * Lb framework Observer Interface component file
 */

namespace lb\components\observers;

use lb\components\listeners\Base;
use lb\components\events\Base;

interface ObserverInterface
{
    protected static $event_listeners = [];

    public static function on($event_name, Base $listener, $data = null);

    public static function trigger($event_name, Base $event);
}
