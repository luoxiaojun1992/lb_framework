<?php

namespace lb\tests\components;

use lb\components\Security;
use lb\tests\BaseTestCase;

class SecurityPasswordTest extends BaseTestCase
{
    protected $str;

    public function setUp()
    {
        parent::setUp();

        $this->str = '123456789';
    }

    public function testGeneratePasswordHash()
    {
        $this->assertNotEmpty(Security::generatePasswordHash($this->str));
    }

    public function testVerifyPassword()
    {

        $this->assertTrue(Security::verifyPassword($this->str, Security::generatePasswordHash($this->str)));
    }
}
