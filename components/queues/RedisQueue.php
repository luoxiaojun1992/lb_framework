<?php

namespace lb\components\queues;

use lb\components\cache\Redis;
use lb\Lb;

class RedisQueue extends BaseQueue
{
    /** @var \Redis */
    private $conn;
    private $key = 'queue';
    private $delayed_key = 'queue:delayed';

    public function push(Job $job)
    {
        $this->conn->rPush($this->key, $this->serialize($job));
    }

    public function pull()
    {
        // Migrating Delayed Queues
        $delayed_queues = $this->conn->zRange($this->delayed_key, 0, -1);
        foreach ($delayed_queues as $delayed_queue) {
            if ($delayed_queue) {
                /** @var Job $job */
                $job = $this->deserialize($delayed_queue);
                if ($job->getExecuteAt() <= date('Y-m-d H:i:s')) {
                    $this->conn->watch($this->delayed_key . '@' . $job->getId());
                    $this->conn->multi();
                    $this->conn->zRem($this->delayed_key, $delayed_queue);
                    $this->push($job);
                    try {
                        $this->conn->exec();
                    } catch (\Exception $e) {
                        $this->conn->discard();
                    }
                }
            }
        }

        $serialized_job = $this->conn->lPop($this->key);
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
        $this->conn = Redis::component()->conn;
        $queue_config = Lb::app()->getQueueConfig();
        if (isset($queue_config['queue'])) {
            $this->key = $queue_config['queue'];
        }
        if (isset($queue_config['queue_delayed'])) {
            $this->delayed_key = $queue_config['queue_delayed'];
        }
    }
}
