<?php

namespace lb\components\queues\handlers;

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
        var_dump($job->getData());
    }
}
