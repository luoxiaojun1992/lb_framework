<?php

namespace lb\components\events;

class BaseEvent implements EventInterface
{
    public $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
