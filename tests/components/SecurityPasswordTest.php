<?php

namespace lb\tests\components;

use lb\components\Security;
use lb\tests\BaseTestCase;

class SecurityPasswordTest extends BaseTestCase
{
    protected $testPassword;

    public function setUp()
    {
        parent::setUp();

        $this->testPassword = '123456789';
    }

    public function testVerifyPassword()
    {

        $this->assertTrue(Security::verifyPassword($this->testPassword, Security::generatePasswordHash($this->testPassword)));
    }
}
