<?php

namespace lb\components\traits\lb;

use lb\components\helpers\SerializeHelper;

trait Serializer
{
    /**
     * Serialize Closure
     *
     * @param \Closure $closure
     * @return null
     */
    public function serializeClosure(\Closure $closure)
    {
        if ($this->isSingle()) {
            return SerializeHelper::component()->serializeClosure($closure);
        }

        return null;
    }

    /**
     * Unserialize Closure
     *
     * @param $serialized
     * @return null
     */
    public function unserializeClosure($serialized)
    {
        if ($this->isSingle()) {
            return SerializeHelper::component()->unserializeClosure($serialized);
        }

        return null;
    }

    /**
     * Serialize
     *
     * @param $serializable
     * @return null
     */
    public function serialize($serializable)
    {
        if ($this->isSingle()) {
            return SerializeHelper::component()->serialize($serializable);
        }

        return null;
    }

    /**
     * Unserialize
     *
     * @param $serialized
     * @return null
     */
    public function unserialize($serialized)
    {
        if ($this->isSingle()) {
            return SerializeHelper::component()->unserialize($serialized);
        }

        return null;
    }
}
