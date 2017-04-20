<?php

namespace lb\tests\queues;

use lb\components\queues\handlers\HandlerInterface;
use Mockery as M;
use lb\components\queues\jobs\Job;
use lb\tests\BaseTestCase;

class JobTest extends BaseTestCase
{
    const JOB_ID = 1;

    /** @var  Job */
    private $job;
    private $handler;
    private $execute_at;

    public function setUp()
    {
        parent::setUp();

        $this->handler = M::mock(HandlerInterface::class);
        $this->execute_at = date('Y-m-d H:i:s', strtotime('+ 1 minute'));
        $this->job = new Job($this->handler, [], self::JOB_ID, $this->execute_at);
    }

    /**
     * Test Get Id
     */
    public function testGetId()
    {
        $this->assertEquals(self::JOB_ID, $this->job->getId());
    }

    /**
     * Get Data
     */
    public function testGetData()
    {
        $this->assertEquals([], $this->job->getData());
    }

    /**
     * Get Handler
     */
    public function testGetHandler()
    {
        $this->assertEquals($this->handler, $this->job->getHandler());
    }

    /**
     * Get Execute At
     */
    public function testGetExecuteAt()
    {
        $this->assertEquals($this->execute_at, $this->job->getExecuteAt());
    }

    public function tearDown()
    {
        parent::tearDown();

        M::close();
    }
}
