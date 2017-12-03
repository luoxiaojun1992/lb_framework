<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\ShareMemory;
use lb\tests\BaseTestCase;

class ShareMemoryTest extends BaseTestCase
{
    public function testOp()
    {
        $shareMemory = new ShareMemory(0x4337b700, ShareMemory::MODE_C, 0644, 256);
        $shareMemory->open();
        $shareMemory->write(pack('a*', 'Hello World'), 0);
        $this->assertEquals('Hello World', unpack('a*', $shareMemory->read(0, 11))[1]);
        $shareMemory->delete();
    }
}
