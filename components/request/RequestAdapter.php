<?php

namespace lb\components\request;

abstract class RequestAdapter extends BaseRequest
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
