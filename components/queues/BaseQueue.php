<?php

namespace lb\components\queues;

use lb\BaseClass;
use lb\components\traits\Singleton;

abstract class BaseQueue extends BaseClass implements QueueInterface
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

    /**
     * Choose the serializer.
     * @var string
     */
    public $serializer = 'json';

    private function __construct()
    {
        $this->init();
    }

    abstract public function push(Job $job);

    abstract public function pull() : Job;

    abstract public function delay(Job $job, $execute_at);

    abstract public function delete(Job $job);

    abstract public function init();
}
