<?php

namespace lb\components\queues\drivers;

use lb\components\queues\jobs\Job;
use lb\Lb;
use nsqphp\Lookup\Nsqlookupd;
use nsqphp\Message\Message;
use nsqphp\nsqphp;

class NsqQueue extends BaseQueue
{
    /**
     * @var nsqphp 
     */
    private $conn;

    /**
     * @var  nsqphp 
     */
    private $pullConn;

    private $key = 'queue';
    private $delayed_key = 'queue:delayed';
    private $hosts;
    private $channel = 'queue';

    public function push(Job $job)
    {
        $hosts = $this->getHosts();
        if (is_array($hosts)) {
            $this->getConn()->publishTo($hosts, nsqphp::PUB_QUORUM)
                ->publish($this->getKey(), new Message($this->serialize($job)));
        } else {
            $this->getConn()->publishTo($hosts)
                ->publish($this->getKey(), new Message($this->serialize($job)));
        }
    }

    public function pull()
    {
        try {
            $this->getPullConn()->subscribe(
                $this->getKey(),
                $this->getChannel(),
                function ($msg) {
                    /**
                * @var Job $job 
                */
                    $job = $this->deserialize($msg->getPayload());
                    if ($job) {
                        $job->handle();
                    }
                }
            )->run();
        } catch (\Throwable $e) {
            $this->setPullConn();
        }

        return null;
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
        $this->setConn(new nsqphp());
        $queue_config = Lb::app()->getQueueConfig();

        if (isset($queue_config['queue'])) {
            $this->setKey($queue_config['queue']);
        }

        if (isset($queue_config['queue_delayed'])) {
            $this->setDelayedKey($queue_config['queue_delayed']);
        }

        $this->setHosts($queue_config['nsq_hosts']);

        if (isset($queue_config['nsq_channel'])) {
            $this->setChannel($queue_config['nsq_channel']);
        }

        $this->setPullConn();
    }

    protected function setPullConn()
    {
        $hosts = $this->getHosts();
        if (is_array($hosts)) {
            $this->pullConn = new nsqphp(new Nsqlookupd(implode(',', $hosts)));
        } else {
            $this->pullConn = new nsqphp(new Nsqlookupd($hosts));
        }
    }

    protected function getPullConn()
    {
        return $this->pullConn;
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

    protected function setHosts($hosts)
    {
        $this->hosts = $hosts;
    }

    protected function getHosts()
    {
        return $this->hosts;
    }

    protected function setChannel($channel)
    {
        $this->channel = $channel;
    }

    protected function getChannel()
    {
        return $this->channel;
    }
}
