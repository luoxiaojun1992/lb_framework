<?php

namespace lb\components\listeners;

use lb\components\events\BaseEvent;

class BaseListener implements ListenerInterface
{
    public $event_data;

    public function handler(BaseEvent $event)
    {
        $this->event_data = $event->data;
    }

    public function getEventData()
    {
        return $this->event_data;
    }
}
