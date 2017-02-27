<?php

namespace lb\components\traits;

trait BaseObject
{
    /**
     * @param array $properties
     */
    public function setProperties(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }
}
