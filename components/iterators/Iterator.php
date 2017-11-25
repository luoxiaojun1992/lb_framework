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
        return array_slice($this->getCollection(), $this->getPosition(), 1)[0] ?? null;
    }

    function key()
    {
        $arr = array_flip(array_slice($this->getCollection(), $this->getPosition(), 1, true));
        return array_pop($arr);
    }

    function next()
    {
        $this->increasePosition();
    }

    function valid()
    {
        $currentValue = $this->current();
        return isset($currentValue);
    }
}
