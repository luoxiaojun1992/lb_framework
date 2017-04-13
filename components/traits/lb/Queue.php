<?php

namespace lb\components\traits\lb;

use lb\components\queues\drivers\BaseQueue;
use lb\components\queues\jobs\Job;

trait Queue
{
    /**
     * Push message to queue
     *
     * @param Job $job
     */
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

    /**
     * Push message to delay queue
     *
     * @param Job $job
     * @param $execute_at
     */
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

    /**
     * Pull message from queue
     *
     * @return null
     */
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
