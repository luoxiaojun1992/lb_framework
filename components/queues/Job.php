<?php

namespace lb\components\queues;

use lb\components\utils\IdGenerator;

class Job implements JobInterface
{
    public $handler;
    public $id;
    public $data;
    public $execute_at;

    public function __construct($handler, $data, $id = 0, $execute_at = '')
    {
        $this->setHandler($handler);
        $this->setId($id ? : IdGenerator::generate());
        $this->setData($data);
        $this->setExecuteAt($execute_at ? : date('Y-m-d H:i:s'));
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
}
