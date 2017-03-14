<?php

namespace lb\components\containers;

class RouteInfo extends Base
{
    public function set($item_name, $item_value)
    {
        $this->$item_name = $item_value;
    }

    public function get($item_name)
    {
        return $this->$item_name;
    }
}
