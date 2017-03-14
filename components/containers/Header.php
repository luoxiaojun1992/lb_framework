<?php

namespace lb\components\containers;

class Header extends Base
{
    public function set($headerKey, $headerValue)
    {
        $this->$headerKey = $headerValue;
    }

    public function get($headerKey)
    {
        return $this->$headerKey;
    }
}
