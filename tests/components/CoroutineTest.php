<?php

namespace lb\tests\components\helpers;

use lb\components\coroutine\Scheduler;
use lb\tests\BaseTestCase;

class CoroutineTest extends BaseTestCase
{
    public function testCoroutine()
    {

        $gen = function(&$count) {
            $count = 0;
            for ($i = 0; $i < 10; ++$i) {
                ++$count;
                yield;
            }
        };

        /** @var Scheduler $scheduler */
        $scheduler = Scheduler::component();
        $count = [];
        for ($i = 0; $i < 10; ++$i) {
            $count[$i] = 0;
            $scheduler->newTask($gen($count[$i]));
        }
        $scheduler->run();
        $this->assertEquals([
            0 => 10,
            1 => 10,
            2 => 10,
            3 => 10,
            4 => 10,
            5 => 10,
            6 => 10,
            7 => 10,
            8 => 10,
            9 => 10,
        ], $count);
    }
}
