<?php

namespace lb\tests\components\observers;

use lb\components\listeners\BaseListener;
use lb\Lb;
use lb\tests\BaseTestCase;

class ObserverTest extends BaseTestCase
{
    protected $event_name;
    protected $event_data;

    public function setUp()
    {
        parent::setUp();

        $this->event_name = 'test';
        $this->event_data = 'test';
    }

    public function testOnTrigger()
    {
        $event_name = $this->event_name;
        $event_data = $this->event_data;
        $listener = new BaseListener();
        Lb::app()->on($event_name, $listener, $event_data);
        Lb::app()->trigger($event_name);
        $this->assertEquals($event_data, $listener->getEventData());
    }
}
