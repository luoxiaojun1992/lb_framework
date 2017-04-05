<?php

namespace lb\components\request;

use lb\BaseClass;

abstract class RequestAdapter extends BaseClass
{
    protected $swooleRequest;

    public function setSwooleRequest($swooleRequest)
    {
        $this->swooleRequest = $swooleRequest;
        return $this;
    }
}
