<?php

namespace lb\components\listeners;

use DebugBar\DataCollector\MessagesCollector;
use lb\components\events\BaseEvent;
use lb\components\traits\Singleton;

class LogWriteListener extends BaseListener
{
    use Singleton;

    protected $context;

    public function handler(BaseEvent $event)
    {
        parent::handler($event);

        /**
 * @var MessagesCollector $messageCollector 
*/
        $messageCollector = $event->getData();

        $logData = $event->getLogData();
        $messageCollector->addMessage($logData['message'], $logData['level']);
    }
}
