<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2016/8/2
 * Time: 11:26
 */

namespace lb\tests\components\distribution;

use lb\components\distribution\FlexiHash;

class FlexiHashTest extends \PHPUnit_Framework_TestCase
{
    protected $flexihash_instance;
    protected $key;
    protected $servers;

    public function setUp()
    {
        parent::setUp();

        $this->flexihash_instance = FlexiHash::component();
        $this->servers = [
            '8.8.8.9',
            '8.8.8.8',
            '8.8.8.7',
            '8.8.8.6',
        ];
        $this->flexihash_instance->addServers($this->servers);

        $this->key = 'test_key';
    }

    public function testLookUp()
    {
        // todo node id to be verified
        $this->assertEquals($this->servers[1], $this->flexihash_instance->lookup($this->key));
    }
}