<?php

namespace lb\components\containers;

use lb\BaseClass;
use lb\components\traits\ArrayOp;
use lb\components\traits\Singleton;

abstract class Base extends BaseClass implements \ArrayAccess
{
    use Singleton;

    use ArrayOp;

    private function __construct()
    {
        //
    }

    public function set($key, $val)
    {
        $this->$key = $val;
    }

    public function get($key)
    {
        return $this->$key;
    }
}
