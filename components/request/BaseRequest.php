<?php

namespace lb\components\request;

use lb\BaseClass;
use lb\components\containers\Header;

abstract class BaseRequest extends BaseClass implements RequestContract
{
    abstract public function getHeaders() : Header;

    public function getHeader($headerKey)
    {
        return $this->getHeaders()->get($headerKey);
    }
}
