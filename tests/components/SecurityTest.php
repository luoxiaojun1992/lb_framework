<?php

namespace lb\tests\components;

use lb\components\Security;
use lb\tests\BaseTestCase;

class SecurityTest extends BaseTestCase
{
    public function testGenerateCsrfToken()
    {
        $this->assertNotEmpty(Security::generateCsrfToken());
    }
}
