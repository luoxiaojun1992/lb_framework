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
        $this->conn->send('/queue/' . $this->key, new Bytes($this->serialize($job)));
    }

    public function pull()
    {
        $this->conn->subscribe('/queue/' . $this->key, 'binary-sub-' . $this->key);
        $msg = $this->conn->read();
        $this->conn->unsubscribe('/queue/' . $this->key, 'binary-sub-' . $this->key);
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
            $this->key = $queue_config['queue'];
        }
        if (isset($queue_config['queue_delayed'])) {
            $this->delayed_key = $queue_config['queue_delayed'];
        }

        $client = new Client($queue_config['activemq_hosts']);
        $client->setLogin($queue_config['activemq_username'], $queue_config['activemq_password']);
        $this->conn = new SimpleStomp($client);
    }
}
