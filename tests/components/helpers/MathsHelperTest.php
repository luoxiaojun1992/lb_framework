<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/2
 * Time: 13:39
 */

namespace lb\tests\components\helpers;

use lb\components\helpers\MathsHelper;

class MathsHelperTest extends \PHPUnit_Framework_TestCase
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
