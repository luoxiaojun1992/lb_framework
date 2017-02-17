<?php

namespace lb\tests\components;

use lb\components\Environment;
use lb\tests\BaseTestCase;

class EnvironmentTest extends BaseTestCase
{
    public function testGetValue()
    {
        $env_name = 'PATH';
        $this->assertNotEmpty(Environment::getValue($env_name));
    }
}
