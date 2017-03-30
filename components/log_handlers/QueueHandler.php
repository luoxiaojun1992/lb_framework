<?php

namespace lb\components\log_handlers;

use lb\components\queues\handlers\LogHandler;
use lb\components\queues\Job;
use lb\Lb;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class QueueHandler extends AbstractProcessingHandler
{
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        Lb::app()->queuePush(new Job(LogHandler::class, [
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U'),
        ]));
    }
}
