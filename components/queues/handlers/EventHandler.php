<?php

namespace lb\components\queues\handlers;

use lb\components\queues\jobs\Job;
use lb\Lb;

class EventHandler implements HandlerInterface
{
    public function __construct()
    {
        //
    }

    public function handle(Job $job)
    {
        $jobData = $job->getData();
        Lb::app()->trigger($jobData['event_name'], $jobData['event'], true);
    }
}
