<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/12/16
 * Time: 9:33
 */

namespace lb\tests\components\widget;

use lb\components\db\mysql\DynamicModel;
use lb\components\widget\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $grid_tpl = <<<Grid
<table %s>
    <thead>%s</thead>
    <tbody>%s</tbody>
</table>
Grid;
        $thead = '<tr><td>ID</td><td>Name</td><td>Not Set</td><td>Only Label</td><td>Not Set</td><td>Label & Value</td><td>Not Set</td></tr>';
        $tbody[] = '<tr><td>1</td><td>b</td><td>24</td><td>Not Set</td><td>Only Value</td><td>Label & Value</td><td>24</td></tr>';
        $tbody[] = '<tr><td>2</td><td>c</td><td>25</td><td>Not Set</td><td>Only Value</td><td>Label & Value</td><td>25</td></tr>';
        $tbody[] = '<tr><td>3</td><td>d</td><td>26</td><td>Not Set</td><td>Only Value</td><td>Label & Value</td><td>26</td></tr>';
        $expectedGrid = sprintf($grid_tpl, 'class="test"', $thead, implode('', $tbody));

        $dataProvider = [];
        $labels = [
            'id' => 'ID',
            'name' => 'Name',
            'age' => 'Age',
        ];
        for ($i = 1; $i <= 3; ++$i) {
            $attributes = [
                'id' => $i,
                'name' => chr(ord('a') + $i),
                'age' => 23 + $i,
            ];
            $testModel = new DynamicModel();
            $testModel->defineTableName('people');
            $testModel->defineAttributes($attributes);
            $testModel->defineLabels($labels);
            $dataProvider[] = $testModel;
        }

        $options = [
            [
                'label' => 'ID',
                'attribute' => 'id',
                'value' => function($value) {
                    return $value;
                },
            ],
            [
                'label' => 'Name',
                'attribute' => 'name',
            ],
            [
                'attribute' => 'age',
            ],
            [
                'label' => 'Only Label',
            ],
            [
                'value' => function() {
                    return 'Only Value';
                },
            ],
            [
                'label' => 'Label & Value',
                'value' => function(){
                    return 'Label & Value';
                },
            ],
            [
                'attribute' => 'age',
                'value' => function($value) {
                    return $value;
                },
            ],
        ];

        $htmlOptions = ['class' => 'test'];

        $actualGrid = Grid::render($dataProvider, $options, $htmlOptions);

        $this->assertEquals($expectedGrid, $actualGrid);
    }
}
