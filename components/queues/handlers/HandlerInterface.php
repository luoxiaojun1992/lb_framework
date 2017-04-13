<?php

namespace lb\components\queues\handlers;

use lb\components\queues\jobs\Job;

interface HandlerInterface
{
    public function __construct();

    public function handle(Job $job);
}
