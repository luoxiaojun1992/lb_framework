<?php

namespace lb\components\traits;

use lb\components\helpers\JsonHelper;
use lb\components\iterators\Iterator;

trait ArrayOp
{
    protected $components = [];

    public function __set($component_name, $component_content)
    {
        $this->components[$component_name] = $component_content;
    }

    public function __get($component_name)
    {
        if (array_key_exists($component_name, $this->components)) {
            return $this->components[$component_name];
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetExists($offset)
    {
        if ($offset) {
            return property_exists('self', $offset) || isset($this->components[$offset]);
        }
        return false;
    }

    public function offsetUnset($offset)
    {
        if ($offset) {
            if (property_exists('self', $offset)) {
                unset($this->{$offset});
            } else {
                if (isset($this->components[$offset])) {
                    unset($this->components[$offset]);
                }
            }
        }
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function iterator()
    {
        return new Iterator($this->components);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return JsonHelper::encode($this->components);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        //
    }
}
