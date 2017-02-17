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
}
