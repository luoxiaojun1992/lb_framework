<?php

namespace lb\components\events;

class LogWriteEvent extends BaseEvent
{
    public $logData;

    public function __construct($logData)
    {
        $this->setLogData($logData);
    }

    /**
     * @param $logData
     * @return $this
     */
    public function setLogData($logData)
    {
        $this->logData = $logData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogData()
    {
        return $this->logData;
    }
}
