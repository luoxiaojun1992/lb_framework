<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\MathsHelper;
use lb\tests\BaseTestCase;

class MathsHelperTest extends BaseTestCase
{
    protected $min;
    protected $max;

    public function setUp()
    {
        parent::setUp();

        $this->min = 0;
        $this->max = 1;
    }

    public function testRandomFloat()
    {
        $min = $this->min;
        $max = $this->max;
        $random_float = MathsHelper::randomFloat($min, $max);
        $this->assertGreaterThanOrEqual($min, $random_float);
        $this->assertLessThanOrEqual($max, $random_float);
    }

    public function testTimes()
    {
        $this->assertEquals(8, MathsHelper::times(2, 4));
        $this->assertEquals(8, MathsHelper::times(2, 4, true));
        $this->assertEquals(6, MathsHelper::times(2, 3));
    }

    public function testDivide()
    {
        $this->assertEquals(2, MathsHelper::divide(8, 4));
        $this->assertEquals(2, MathsHelper::divide(8, 4, true));
        $this->assertEquals(2, MathsHelper::divide(6, 3));
    }
}
