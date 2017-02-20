<?php

namespace lb\components\listeners;

use lb\components\events\BaseEvent;

interface ListenerInterface
{
    public function handler(BaseEvent $event);

    public function getEventData();
}
