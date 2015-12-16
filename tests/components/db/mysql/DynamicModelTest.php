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

    public function testUndefineTableName()
    {
        $testModel = new DynamicModel();
        $testModel->defineTableName('test');
        $this->assertEquals('test', $testModel->table_name);
        $testModel->undefineTableName();
        $this->assertEquals('', $testModel->table_name);
    }

    public function testDefineAttribute()
    {
        $testModel = new DynamicModel();
        $testModel->defineAttribute('name', 'a');
        $this->assertEquals('a', $testModel->name);
    }

    public function testUndefineAttribute()
    {
        $testModel = new DynamicModel();
        $testModel->defineAttribute('name', 'a');
        $this->assertEquals('a', $testModel->name);
        $testModel->undefineAttribute('name');
        $this->assertFalse($testModel->name);
    }

    public function testDefineAttributes()
    {
        $attributes = [
            'name' => 'a',
            'age' => 23,
        ];
        $testModel = new DynamicModel();
        $testModel->defineAttributes($attributes);
        $this->assertEquals($attributes, $testModel->getAttributes());
    }

    public function testUndefineAttributes()
    {
        $attributes = [
            'name' => 'a',
            'age' => 23,
        ];
        $testModel = new DynamicModel();
        $testModel->defineAttributes($attributes);
        $this->assertEquals($attributes, $testModel->getAttributes());
        $attributes = array_keys($attributes);
        $testModel->undefineAttributes($attributes);
        $this->assertEquals([], $testModel->getAttributes());

        $attributes2 = [
            'id' => 0,
            'name' => 'a',
            'age' => '23',
        ];
        $testModel->defineAttributes($attributes2);
        $this->assertEquals($attributes2, $testModel->getAttributes());
        $testModel->undefineAttributes(['name', 'age']);
        $this->assertEquals(['id' => 0], $testModel->getAttributes());
    }

    public function testDefineLabel()
    {
        $testModel = new DynamicModel();
        $testModel->defineLabel('name', 'Name');
        $this->assertEquals(['name' => 'Name'], $testModel->getLabels());
    }

    public function testUndefineLabel()
    {
        $testModel = new DynamicModel();
        $testModel->defineLabel('name', 'Name');
        $this->assertEquals(['name' => 'Name'], $testModel->getLabels());
        $testModel->undefineLabel('name');
        $this->assertEquals([], $testModel->getLabels());
    }

    public function testDefineLabels()
    {
        $labels = [
            'id' => 'ID',
            'name' => 'Name',
        ];
        $testModel = new DynamicModel();
        $testModel->defineLabels($labels);
        $this->assertEquals($labels, $testModel->getLabels());
    }

    public function testUndefineLabels()
    {
        $labels = [
            'id' => 'ID',
            'name' => 'Name',
        ];
        $testModel = new DynamicModel();
        $testModel->defineLabels($labels);
        $this->assertEquals($labels, $testModel->getLabels());
        $labels = array_keys($labels);
        $testModel->undefineLabels($labels);
        $this->assertEquals([], $testModel->getLabels());

        $labels2 = [
            'id' => 'ID',
            'name' => 'Name',
            'age' => 'Age',
        ];
        $testModel->defineLabels($labels2);
        $this->assertEquals($labels2, $testModel->getLabels());
        $testModel->undefineLabels(['name', 'age']);
        $this->assertEquals(['id' => 'ID'], $testModel->getLabels());
    }
}
