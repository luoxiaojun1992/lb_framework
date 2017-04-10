<?php

namespace lb\components\containers;

use lb\BaseClass;
use lb\components\iterators\Iterator;
use lb\components\traits\ArrayOp;
use lb\components\traits\Singleton;

class Base extends BaseClass implements \ArrayAccess
{
    use Singleton;

    use ArrayOp;

    private function __construct()
    {
        //
    }

    public function iterator()
    {
        return new Iterator($this);
    }
}
