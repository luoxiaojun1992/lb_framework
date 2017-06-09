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

    public function testYuan2Fen()
    {
        $this->assertEquals(80, MoneyHelper::yuan2Fen(0.8));
    }

    public function testFen2Yuan()
    {
        $this->assertEquals(0.8, MoneyHelper::fen2Yuan(80));
    }

    public function testYuanAdd()
    {
        $this->assertEquals(0.8, MoneyHelper::yuanAdd(0.1, 0.7));
        $this->assertEquals(0.6, MoneyHelper::yuanAdd(0.7, -0.1));
    }
}
