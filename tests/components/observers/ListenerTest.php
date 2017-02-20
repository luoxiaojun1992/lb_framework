<?php

namespace lb\tests\components\observers;

use lb\components\events\BaseEvent;
use lb\components\listeners\BaseListener;
use lb\tests\BaseTestCase;

class ListenerTest extends BaseTestCase
{
    private $event_data;

    public function setUp()
    {
        parent::setUp();

        $this->event_data = 'test';
    }

    public function testGetEventData()
    {
        $listener = new BaseListener();
        $event = new BaseEvent();
        $event->data = $this->event_data;
        $listener->handler($event);
        $this->assertEquals($this->event_data, $listener->getEventData());
    }
}
