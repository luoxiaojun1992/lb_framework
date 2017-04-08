<?php

namespace lb\components\queues;

use lb\BaseClass;
use lb\components\traits\Singleton;
use lb\Lb;

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
    public $serializer = 'php';

    private function __construct()
    {
        $this->init();
    }

    /**
     * Serializer
     *
     * @param $unserialized_data
     * @return string
     */
    protected function serialize($unserialized_data)
    {
        switch ($this->serializer) {
            case self::SERIALIZER_JSON:
                break;
            case self::SERIALIZER_PHP:
                return Lb::app()->serialize($unserialized_data);
        }

        return $unserialized_data;
    }

    /**
     * Deserializer
     *
     * @param $serialized_data
     * @return mixed
     */
    protected function deserialize($serialized_data)
    {
        switch ($this->serializer) {
            case self::SERIALIZER_JSON:
                break;
            case self::SERIALIZER_PHP:
                return Lb::app()->unserialize($serialized_data);
        }

        return $serialized_data;
    }

    abstract public function push(Job $job);

    abstract public function pull();

    abstract public function delay(Job $job, $execute_at);

    abstract public function delete(Job $job);

    abstract public function init();
}
