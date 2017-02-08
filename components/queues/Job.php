<?php

namespace lb\components\queues;

class Job implements JobInterface
{
    public $handler;
    public $id;
    public $data;

    public function __construct(Callable $handler, $id, $data)
    {
        $this->setHandler($handler);
        $this->setId($id);
        $this->setData($data);
    }

    public function setHandler(Callable $handler)
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

    public function getHandler()
    {
        return $this->handler;
    }
}
