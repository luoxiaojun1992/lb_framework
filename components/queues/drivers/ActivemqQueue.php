<?php

namespace lb\components\queues\drivers;

use lb\components\queues\jobs\Job;
use lb\Lb;

class ActivemqQueue extends BaseQueue
{
    private $conn;
    private $key = 'queue';
    private $delayed_key = 'queue:delayed';

    public function push(Job $job)
    {
        //
    }

    public function pull()
    {
        //

        if (!$serialized_job) {
            return null;
        }
        return $this->deserialize($serialized_job);
    }

    public function delay(Job $job, $execute_at)
    {
        $job->setExecuteAt($execute_at);
        $this->push($job);
    }

    public function delete(Job $job)
    {
        return true;
    }

    public function init()
    {
        $queue_config = Lb::app()->getQueueConfig();
        if (isset($queue_config['queue'])) {
            $this->key = $queue_config['queue'];
        }
        if (isset($queue_config['queue_delayed'])) {
            $this->delayed_key = $queue_config['queue_delayed'];
        }
        //
    }
}
