<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\SerializeHelper;
use lb\tests\BaseTestCase;

class SerializeHelperTest extends BaseTestCase
{
    public function testSerializeUnserializeClosure()
    {
        $testClosure = function () {};
        $this->assertEquals(
            $testClosure,
            SerializeHelper::component()->unserializeClosure(SerializeHelper::component()->serializeClosure($testClosure))
        );
    }
}
