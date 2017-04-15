<?php

namespace lb\components\queues\drivers;

use lb\components\queues\jobs\Job;
use lb\Lb;
use nsqphp\Lookup\Nsqlookupd;
use nsqphp\Message\Message;
use nsqphp\nsqphp;

class NsqQueue extends BaseQueue
{
    /** @var nsqphp */
    private $conn;

    /** @var  nsqphp */
    private $pullConn;

    private $key = 'queue';
    private $delayed_key = 'queue:delayed';
    private $hosts = [];
    private $channel = 'queue';

    public function push(Job $job)
    {
        $this->conn->publishTo($this->hosts, nsqphp::PUB_QUORUM)
            ->publish($this->key, new Message($this->serialize($job)));
    }

    public function pull()
    {
        $serialized_job = null;

        $nsqphp = $this->pullConn;
        $nsqphp->subscribe(
            $this->key,
            $this->channel,
            function($msg) use (&$serialized_job, $nsqphp) {
                $serialized_job = $msg->getPayload();
                $nsqphp->stop();
            }
        )->run();

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
        $this->conn = new nsqphp();
        $queue_config = Lb::app()->getQueueConfig();

        if (isset($queue_config['queue'])) {
            $this->key = $queue_config['queue'];
        }

        if (isset($queue_config['queue_delayed'])) {
            $this->delayed_key = $queue_config['queue_delayed'];
        }

        $this->hosts = $queue_config['nsq_hosts'];

        if (isset($queue_config['nsq_channel'])) {
            $this->channel = $queue_config['nsq_channel'];
        }

        $this->pullConn = new nsqphp(new Nsqlookupd($this->hosts));
    }
}
