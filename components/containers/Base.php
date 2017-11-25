<?php

namespace lb\components\containers;

use lb\BaseClass;
use lb\components\traits\ArrayOp;
use lb\components\traits\Singleton;

class Base extends BaseClass implements \ArrayAccess, \Serializable
{
    use Singleton;

    use ArrayOp;

    public function set($key, $val)
    {
        $this->$key = $val;
    }

    public function get($key)
    {
        return $this->$key;
    }
}
