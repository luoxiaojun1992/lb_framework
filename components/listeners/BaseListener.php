<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/5
 * Time: 14:26
 * Lb framework Base Listener component file
 */

namespace lb\components\listeners;

use lb\components\events\BaseEvent;

class BaseListener implements ListenerInterface
{
    public $event_data;

    public function handler(BaseEvent $event)
    {
        $this->event_data = $event->data;
    }
}
