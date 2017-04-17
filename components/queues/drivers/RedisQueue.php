<?php

namespace lb\components\queues\drivers;

use lb\components\cache\Redis;
use lb\components\queues\jobs\Job;
use lb\Lb;

class RedisQueue extends BaseQueue
{
    /** @var \Redis */
    private $conn;
    private $key = 'queue';
    private $delayed_key = 'queue:delayed';

    public function push(Job $job)
    {
        $this->getConn()->rPush($this->getKey(), $this->serialize($job));
    }

    public function pull()
    {
        $conn = $this->getConn();
        // Migrating Delayed Queues
        $delayed_queues = $conn->zRange($this->getDelayedKey(), 0, -1);
        foreach ($delayed_queues as $delayed_queue) {
            if ($delayed_queue) {
                /** @var Job $job */
                $job = $this->deserialize($delayed_queue);
                if ($job->getExecuteAt() <= date('Y-m-d H:i:s')) {
                    $conn->watch($this->getDelayedKey() . '@' . $job->getId());
                    $conn->multi();
                    $conn->zRem($this->getDelayedKey(), $delayed_queue);
                    $this->push($job);
                    try {
                        $conn->exec();
                    } catch (\Exception $e) {
                        $conn->discard();
                    }
                }
            }
        }

        $serialized_job = $conn->lPop($this->getKey());
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
        $this->setConn(Redis::component()->conn);
        $queue_config = Lb::app()->getQueueConfig();
        if (isset($queue_config['queue'])) {
            $this->setKey($queue_config['queue']);
        }
        if (isset($queue_config['queue_delayed'])) {
            $this->setDelayedKey($queue_config['queue_delayed']);
        }
    }

    protected function setConn($conn)
    {
        $this->conn = $conn;
    }

    protected function getConn()
    {
        return $this->conn;
    }

    protected function setKey($queue)
    {
        $this->key = $queue;
    }

    protected function getKey()
    {
        return $this->key;
    }

    protected function setDelayedKey($delayedQueue)
    {
        $this->delayed_key = $delayedQueue;
    }

    protected function getDelayedKey()
    {
        return $this->delayed_key;
    }
}
