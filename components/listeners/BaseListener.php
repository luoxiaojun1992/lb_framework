<?php

namespace lb\components\listeners;

use lb\components\events\BaseEvent;

class BaseListener implements ListenerInterface
{
    public $event_data;

    public function handler(BaseEvent $event)
    {
        $this->setEventData($event->getData());
    }

    public function setEventData($eventData)
    {
        $this->event_data = $eventData;
    }

    public function getEventData()
    {
        return $this->event_data;
    }
}
