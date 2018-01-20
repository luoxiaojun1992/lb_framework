<?php

namespace lb\components\listeners;

use DebugBar\DataCollector\MessagesCollector;
use lb\components\events\BaseEvent;
use lb\components\traits\Singleton;
use Monolog\Logger;

class LogWriteListener extends BaseListener
{
    const LOG_LEVELS = [
        Logger::DEBUG => 'debug',
        Logger::INFO => 'info',
        Logger::NOTICE => 'notice',
        Logger::WARNING => 'warning',
        Logger::ERROR => 'error',
        Logger::CRITICAL => 'critical',
        Logger::ALERT => 'alert',
        Logger::EMERGENCY => 'emergency',
    ];

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
        $messageCollector->addMessage($logData['message'], self::LOG_LEVELS[$logData['level']] ?? 'info');
    }
}
