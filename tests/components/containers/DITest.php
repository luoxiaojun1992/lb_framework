<?php

namespace lb\tests\components\containers;

use lb\Lb;
use lb\tests\BaseTestCase;
use Mockery as M;

class DITest extends BaseTestCase
{
    protected $service;
    protected $container;

    public function setUp()
    {
        parent::setUp();

        $this->service = M::mock('service');
        $this->service
            ->shouldReceive('test')
            ->with(1)
            ->andReturn(1);
        $this->container = Lb::app()->getDIContainer();
    }

    public function testSetGet()
    {
        $this->container->set('service', $this->service);
        $service = $this->container->get('service');
        $this->assertEquals(1, $service->test(1));
    }

    public function tearDown()
    {
        parent::tearDown();

        M::close();
    }
}
