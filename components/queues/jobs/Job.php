<?php

namespace lb\components\queues\jobs;

use lb\Lb;

class Job implements JobInterface
{
    public $handler;
    public $id;
    public $data;
    public $execute_at;
    public $tryTimes = 5; //Default try times is 5.
    public $triedTimes = 0;

    public function __construct($handler, $data, $id = 0, $execute_at = '', $tryTimes = 5)
    {
        $this->setHandler($handler);
        $this->setId($id ? : Lb::app()->uniqid('queue_'));
        $this->setData($data);
        $this->setExecuteAt($execute_at ? : date('Y-m-d H:i:s'));
        $this->setTryTimes($tryTimes);
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setExecuteAt($execute_at)
    {
        $this->execute_at = $execute_at;
    }

    public function getExecuteAt()
    {
        return $this->execute_at;
    }

    public function setTryTimes($tryTimes)
    {
        $this->tryTimes = $tryTimes;
    }

    public function getTryTimes()
    {
        return $this->tryTimes;
    }

    public function setTriedTimes($triedTimes)
    {
        $this->triedTimes = $triedTimes;
    }

    public function getTriedTimes()
    {
        return $this->triedTimes;
    }

    public function canTry()
    {
        return $this->getTriedTimes() < $this->getTryTimes();
    }

    public function addTriedTimes($step = 1)
    {
        $this->setTriedTimes($this->getTriedTimes() + $step);
    }

    public function handle()
    {
        $this->addTriedTimes();
        $pid = pcntl_fork();
        if ($pid == -1) {
            $this->canTry() && Lb::app()->queuePush($this);
        } else if ($pid == 0) {
            $handler_class = $this->getHandler();
            if (class_exists('\Throwable')) {
                try {
                    (new $handler_class)->handle($this);
                } catch (\Throwable $e) {
                    $this->canTry() && Lb::app()->queuePush($this);
                    echo $e->getTraceAsString() . PHP_EOL;
                }
            } else {
                try {
                    (new $handler_class)->handle($this);
                } catch (\Exception $e) {
                    $this->canTry() && Lb::app()->queuePush($this);
                    echo $e->getTraceAsString() . PHP_EOL;
                }
            }
            Lb::app()->stop();
        } else {
            pcntl_wait($status);
            echo 'Processed job ' . $this->getId() . PHP_EOL;
        }
    }
}
