<?php

namespace lb\components\containers;

class Config extends Base
{
    public function set($config_name, $config_content)
    {
        $this->$config_name = $config_content;
    }

    public function get($config_name)
    {
        return $this->$config_name;
    }
}
