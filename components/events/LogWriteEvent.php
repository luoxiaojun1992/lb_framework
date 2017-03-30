<?php

namespace lb\components\events;

class LogWriteEvent extends BaseEvent
{
    public $logData;

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
