<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/24
 * Time: 10:36
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\SystemHelper;
use lb\Lb;

class SystemHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetVersion()
    {
        $expectedVersion = Lb::VERSION;
        $actualVersion = SystemHelper::getVersion();
        $this->assertEquals($expectedVersion, $actualVersion);
    }
}
