<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\SystemHelper;
use lb\Lb;
use lb\tests\BaseTestCase;

class SystemHelperTest extends BaseTestCase
{
    public function testGetVersion()
    {
        $expectedVersion = Lb::VERSION;
        $actualVersion = SystemHelper::getVersion();
        $this->assertEquals($expectedVersion, $actualVersion);
    }
}
