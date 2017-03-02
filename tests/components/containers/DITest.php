<?php

namespace lb\tests\components\containers;

use lb\components\containers\DI;
use lb\Lb;
use lb\tests\BaseTestCase;
use Mockery as M;

class DITest extends BaseTestCase
{
    protected $service;
    /** @var  DI */
    protected $container;
    protected $callFunc;

    public function setUp()
    {
        parent::setUp();

        $this->service = M::mock('service');
        $this->service
            ->shouldReceive('test')
            ->with(1)
            ->andReturn(1);

        $this->callFunc = function () {
            return 1;
        };

        $this->container = Lb::app()->getDIContainer();
    }

    public function testSetGet()
    {
        //Inject string
        $this->container->set('string', 'test');
        $this->assertEquals('test', $this->container->get('string'));

        //Inject integer
        $this->container->set('integer', 1);
        $this->assertEquals(1, $this->container->get('integer'));

        //Inject callable
        $this->container->set('callable', $this->callFunc);
        $this->assertEquals(1, $this->container->get('callable'));

        //Inject Object
        $this->container->set('service', $this->service);
        $this->assertEquals(1, $this->container->get('service')->test(1));

        //Inject Class Name
        $this->container->set('std_class_interface', \stdClass::class);
        $this->assertEquals(\stdClass::class, get_class($this->container->get('std_class_interface')));

        //Get Class Name
        $this->assertEquals(\stdClass::class, get_class($this->container->get(\stdClass::class)));

        //Get Callable
        $this->assertEquals(1, $this->container->get($this->callFunc));

        //Get String
        $this->assertEquals('test string', $this->container->get('test string'));

        //Get Object
        $this->assertEquals(1, $this->container->get($this->service)->test(1));
    }

    public function tearDown()
    {
        parent::tearDown();

        M::close();
    }
}
