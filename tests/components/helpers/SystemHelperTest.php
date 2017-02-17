<?php

namespace lb\tests\components\helpers;

use lb\components\consts\Info;
use lb\components\helpers\SystemHelper;
use lb\tests\BaseTestCase;

class SystemHelperTest extends BaseTestCase
{
    public function testGetVersion()
    {
        $this->assertEquals(Info::VERSION, SystemHelper::getVersion());
    }
}
