<?php

namespace lb\tests;

use lb\components\containers\Base;
use lb\components\containers\Collection;

class IteratorTest extends BaseTestCase
{
    public function testContainerIterator()
    {
        /** @var Base $container */
        $container = new Base();
        $data = ['first' => 'a', 'second' => 'b'];
        $values = array_values($data);
        foreach ($data as $key => $val) {
            $container->set($key, $val);
        }
        $iterator = $container->iterator();
        $position = 0;
        $this->assertEquals('first', $iterator->key());
        $this->assertEquals('a', $iterator->current());
        while($iterator->valid()) {
            $this->assertEquals($values[$position], $iterator->current());
            $iterator->next();
            ++$position;
        }
    }
}
