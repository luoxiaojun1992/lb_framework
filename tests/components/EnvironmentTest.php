<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 14:50
 */

namespace lb\tests\components;

use lb\components\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $env_name = 'PATH';
        $this->assertThat($this->logicalNot($this->assertEmpty(Environment::getValue($env_name))));
    }
}
