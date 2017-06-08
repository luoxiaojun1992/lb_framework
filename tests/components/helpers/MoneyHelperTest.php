<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\MoneyHelper;
use lb\tests\BaseTestCase;

class MoneyHelperTest extends BaseTestCase
{
    public function testNum2rmb()
    {
        $this->assertEquals('壹佰元整', MoneyHelper::num2rmb(100));
    }
}
