<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/3
 * Time: 11:26
 */

namespace lb\tests\components\containers;

use Mockery as M;

class DITest extends \PHPUnit_Framework_TestCase
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

