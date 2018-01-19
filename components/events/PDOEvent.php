<?php

namespace lb\components\events;

class PDOEvent extends BaseEvent
{
    public $statement;
    public $duration;

    public function __construct($logData)
    {
        $this->setLogData($logData);
    }

    public function setLogData($logData)
    {
        $this->logData = $logData;
    }

    public function getLogData()
    {
        return $this->logData;
    }
}
