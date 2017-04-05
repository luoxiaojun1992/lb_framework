<?php

namespace lb\components\request;

use lb\BaseClass;

abstract class RequestAdapter extends BaseClass
{
    protected $swooleRequest;

    protected $sessionId;

    public function setSwooleRequest($swooleRequest)
    {
        $this->swooleRequest = $swooleRequest;
        return $this;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }
}