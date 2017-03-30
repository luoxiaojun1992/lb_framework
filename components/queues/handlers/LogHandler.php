<?php

namespace lb\components\queues\handlers;

use lb\components\db\mysql\Connection;
use lb\components\queues\HandlerInterface;
use lb\components\queues\Job;

class LogHandler implements HandlerInterface
{
    public function __construct()
    {
        //
    }

    public function handle(Job $job)
    {
        Connection::component()->write_conn
            ->prepare('INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)')
            ->execute($job->getData());
    }
}
