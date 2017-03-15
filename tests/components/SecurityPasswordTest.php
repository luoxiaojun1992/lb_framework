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

    public function testGeneratePasswordHash()
    {
        if (function_exists('password_hash')) {
            $passwordHash = password_hash($this->testPassword, PASSWORD_DEFAULT);
        } else {
            $passwordHash = md5($this->testPassword);
        }

        $this->assertEquals($passwordHash, Security::generatePasswordHash($this->testPassword));
    }

    public function testVerifyPassword()
    {

        $this->assertTrue(Security::verifyPassword($this->testPassword, Security::generatePasswordHash($this->testPassword)));
    }
}
