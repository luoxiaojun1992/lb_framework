<?php

namespace lb\components\traits;

trait ArrayOp
{
    protected $components = [];

    public function __set($component_name, $component_content)
    {
        if ($component_name && !property_exists('self', $component_name)) {
            $this->components[$component_name] = $component_content;
        }
    }

    public function __get($component_name)
    {
        if ($component_name && !property_exists('self', $component_name)) {
            if (array_key_exists($component_name, $this->components)) {
                return $this->components[$component_name];
            }
        }
        return false;
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
}
