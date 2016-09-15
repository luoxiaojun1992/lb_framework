<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/7/30
 * Time: 14:31
 */

namespace lb\tests\components;

use lb\components\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParams()
    {
        $expectedParams = [10, 10];
        $actualParams = Pagination::getParams(101, 10, 2);
        $this->assertEquals($expectedParams, $actualParams);
    }
}
