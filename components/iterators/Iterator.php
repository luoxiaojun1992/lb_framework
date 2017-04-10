<?php

namespace lb\components\iterators;

class Iterator implements \Iterator
{
    protected $position = 0;
    protected $collection;

    public function __construct($collection) {
        $this->position = 0;
        $this->collection = $collection;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return array_slice(array_values($this->collection), $this->position, 1)[0];
    }

    function key() {
        return array_slice(array_keys($this->collection), $this->position, 1)[0];
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset(array_slice($this->collection, $this->position, 1)[0]);
    }
}
