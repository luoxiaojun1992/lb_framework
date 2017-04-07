<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\components\traits\Singleton;
use SuperClosure\Serializer;

class SerializeHelper extends BaseClass
{
    use Singleton;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct()
    {
        $this->setSerializer(new Serializer());
    }

    /**
     * Set serializer
     *
     * @param $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serializer
     *
     * @return Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Serialize closure
     *
     * @param \Closure $closure
     * @return string
     */
    public function serializeClosure(\Closure $closure)
    {
        return $this->getSerializer()->serialize($closure);
    }

    /**
     * Unserialize closure
     *
     * @param $serialized
     * @return \Closure
     */
    public function unserializeClosure($serialized) : \Closure
    {
        return $this->getSerializer()->unserialize($serialized);
    }
}
