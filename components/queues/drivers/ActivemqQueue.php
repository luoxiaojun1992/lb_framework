<?php

namespace lb\components\queues\drivers;

use lb\components\queues\jobs\Job;
use lb\Lb;
use Stomp\Client;
use Stomp\SimpleStomp;
use Stomp\Transport\Bytes;

class ActivemqQueue extends BaseQueue
{
    /** @var SimpleStomp */
    private $conn;
    private $key = 'queue';
    private $delayed_key = 'queue:delayed';

    public function push(Job $job)
    {
        $this->getConn()->send('/queue/' . $this->getKey(), new Bytes($this->serialize($job)));
    }

    public function pull()
    {
        $conn = $this->getConn();
        $conn->subscribe('/queue/' . $this->getKey(), 'binary-sub-' . $this->getKey());
        $msg = $conn->read();
        $conn->unsubscribe('/queue/' . $this->getKey(), 'binary-sub-' . $this->getKey());
        $serialized_job = $msg->body;

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
            $this->setKey($queue_config['queue']);
        }
        if (isset($queue_config['queue_delayed'])) {
            $this->setDelayedKey($queue_config['queue_delayed']);
        }

        $client = new Client($queue_config['activemq_hosts']);
        $client->setLogin($queue_config['activemq_username'], $queue_config['activemq_password']);
        $this->setConn(new SimpleStomp($client));
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
