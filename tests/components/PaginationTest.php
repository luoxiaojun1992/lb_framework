<?php

namespace lb\tests\components;

use lb\components\Pagination;
use lb\tests\BaseTestCase;

class PaginationTest extends BaseTestCase
{
    public function testGetParams()
    {
        $expectedParams = [10, 10];
        $actualParams = Pagination::getParams(101, 10, 2);
        $this->assertEquals($expectedParams, $actualParams);
    }
}
