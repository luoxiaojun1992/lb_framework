<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/5
 * Time: 14:26
 * Lb framework Observer Interface component file
 */

namespace lb\components\observers;

use lb\components\listeners\BaseListener;

interface ObserverInterface
{
    public static function on($event_name, BaseListener $listener, $data = null);

    public static function trigger($event_name, $event = null);
}
