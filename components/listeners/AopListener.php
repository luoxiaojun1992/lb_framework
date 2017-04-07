<?php

namespace lb\components\listeners;

use lb\components\events\BaseEvent;
use lb\components\traits\Singleton;

class AopListener extends BaseListener
{
    use Singleton;

    protected $context;

    public function handler(BaseEvent $event)
    {
        parent::handler($event);

        $this->setContext($event->getContext());

        $this->callAopClosure();
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function callAopClosure()
    {
        $eventData = $this->getEventData();
        call_user_func($eventData['closure'], $this->getContext());
    }
}
