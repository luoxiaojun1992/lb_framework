<?php

namespace lb\components\traits\lb;

use lb\components\queues\BaseQueue;
use lb\components\queues\Job;

trait Queue
{
    // Push message to queue
    public function queuePush(Job $job)
    {
        if ($this->isSingle()) {
            $queue_config = $this->getQueueConfig();
            if (isset($queue_config['driver'])) {
                /** @var BaseQueue $driver */
                $driver = $queue_config['driver'];
                $driver::component()->push($job);
            }
        }
    }

    // Push message to delay queue
    public function queueDelay(Job $job, $execute_at)
    {
        if ($this->isSingle()) {
            $queue_config = $this->getQueueConfig();
            if (isset($queue_config['driver'])) {
                /** @var BaseQueue $driver */
                $driver = $queue_config['driver'];
                $driver::component()->delay($job, $execute_at);
            }
        }
    }

    // Pull message from queue
    public function queuePull()
    {
        if ($this->isSingle()) {
            $queue_config = $this->getQueueConfig();
            if (isset($queue_config['driver'])) {
                /** @var BaseQueue $driver */
                $driver = $queue_config['driver'];
                return $driver::component()->pull();
            }
        }

        return null;
    }
}