<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\StringHelper;
use lb\tests\BaseTestCase;

class StringHelperTest extends BaseTestCase
{
    public function testIsCapital()
    {
        $this->assertEquals(false, StringHelper::isCapital('a'));
        $this->assertEquals(true, StringHelper::isCapital('A'));
    }

    public function testCamel()
    {
        $this->assertEquals('FooBar', StringHelper::camel('foo_bar'));
        $this->assertEquals('FooBar', StringHelper::camel('fooBar'));
    }

    public function testUnderLine()
    {
        $this->assertEquals('foo_bar', StringHelper::underLine('FooBar'));
        $this->assertEquals('foo_bar', StringHelper::underLine('fooBar'));
    }
}
