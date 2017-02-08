<?php

namespace lb\components\queues;

use lb\BaseClass;
use lb\components\traits\Singleton;

abstract class BaseQueue extends BaseClass
{
    use Singleton;

    /**
     * Json serializer.
     */
    const SERIALIZER_JSON = 'json';

    /**
     * PHP serializer.
     */
    const SERIALIZER_PHP = 'php';
}
