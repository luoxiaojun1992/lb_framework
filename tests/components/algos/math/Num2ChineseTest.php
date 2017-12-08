<?php

namespace lb\tests\components\algos\math;

use lb\components\algos\math\Num2Chinese;
use lb\tests\BaseTestCase;

class Num2ChineseTest extends BaseTestCase
{
    private $num;

    /** @var Num2Chinese */
    private $num2Chinese;

    public function setUp()
    {
        parent::setUp();

        $this->num = '321320301';

        $this->num2Chinese = new Num2Chinese($this->num);
    }

    public function testNum2Chinese()
    {
        $this->assertEquals('三亿二千一百三十二万零三百零一', $this->num2Chinese->number2Chinese());
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->num2Chinese);
    }
}
