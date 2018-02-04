<?php

namespace lb\components\queues\handlers;

use lb\components\db\mysql\Connection;
use lb\components\queues\jobs\Job;

class LogHandler implements HandlerInterface
{
    public function __construct()
    {
        //
    }

    public function handle(Job $job)
    {
        Connection::component()->write_conn->exec('CREATE TABLE IF NOT EXISTS monolog '
            . '(channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
        );

        Connection::component()->write_conn
            ->prepare('INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)')
            ->execute($job->getData());
    }
}
