<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 11:38
 */

namespace lb\tests\components\db\mysql;

use lb\components\db\mysql\DynamicModel;

class DynamicModelTest extends \PHPUnit_Framework_TestCase
{
    public function testDefineTableName()
    {
        $testModel = new DynamicModel();
        $testModel->defineTableName('test');
        $this->assertEquals('test', $testModel->table_name);
    }
}
