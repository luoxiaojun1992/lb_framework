<?php

namespace lb\components\containers;

class Cookie extends Base
{
    public function set($cookieKey, $cookieValue)
    {
        $this->$cookieKey = $cookieValue;
    }

    public function get($cookieKey)
    {
        return $this->$cookieKey;
    }
}
