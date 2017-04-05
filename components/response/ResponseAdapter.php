<?php

namespace lb\components\response;

use lb\BaseClass;

abstract class ResponseAdapter extends BaseClass
{
    protected $swooleResponse;

    protected $sessionId;

    public function setSwooleResponse($swooleResponse)
    {
        $this->swooleResponse = $swooleResponse;
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
