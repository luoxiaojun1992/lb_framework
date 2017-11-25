<?php

namespace lb\components\iterators;

class Iterator implements \Iterator
{
    protected $position = 0;
    protected $collection;

    public function __construct($collection)
    {
        $this->rewind();
        $this->setCollection($collection);
    }

    function setCollection($collection)
    {
        $this->collection = $collection;
    }

    function getCollection()
    {
        return $this->collection;
    }

    function setPosition($position)
    {
        $this->position = $position;
    }

    function getPosition()
    {
        return $this->position;
    }

    function increasePosition()
    {
        ++$this->position;
    }

    function rewind()
    {
        $this->setPosition(0);
    }

    function current()
    {
//        return array_pop(array_slice($this->getCollection(), $this->getPosition(), 1));
        return array_slice(array_values($this->getCollection()), $this->getPosition(), 1)[0];
    }

    function key()
    {
        return array_slice(array_keys($this->getCollection()), $this->getPosition(), 1)[0];
    }

    function next()
    {
        $this->increasePosition();
    }

    function valid()
    {
        return isset(array_values(array_slice($this->getCollection(), $this->getPosition(), 1))[0]);
    }
}
