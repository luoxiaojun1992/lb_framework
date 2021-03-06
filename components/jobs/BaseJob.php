<?php

namespace lb\components\jobs;

use lb\BaseClass;

abstract class BaseJob extends BaseClass implements JobInterface
{
    protected $data;

    public function handler($data)
    {
        $this->setData($data);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
