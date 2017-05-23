<?php

namespace lb\tests\components\helpers;

use lb\components\helpers\AlgoHelper;
use lb\tests\BaseTestCase;

class AlgoHelperTest extends BaseTestCase
{
    protected $binaryTree;

    protected $reversedTree;

    public function setUp()
    {
        parent::setUp();

        $this->binaryTree = [
            [
                'value' => 1,
                'children' => [
                    [
                        'value' => 2,
                        'children' => [
                            [
                                'value' => 4,
                                'children' => []
                            ],
                            [
                                'value' => 5,
                                'children' => []
                            ],
                        ]
                    ],
                    [
                        'value' => 3,
                        'children' => [
                            [
                                'value' => 6,
                                'children' => []
                            ],
                            [
                                'value' => 7,
                                'children' => []
                            ],
                        ]
                    ],
                ]
            ]
        ];

        $this->reversedTree = [
            [
                'value' => 1,
                'children' => [
                    [
                        'value' => 3,
                        'children' => [
                            [
                                'value' => 7,
                                'children' => []
                            ],
                            [
                                'value' => 6,
                                'children' => []
                            ],
                        ]
                    ],
                    [
                        'value' => 2,
                        'children' => [
                            [
                                'value' => 5,
                                'children' => []
                            ],
                            [
                                'value' => 4,
                                'children' => []
                            ],
                        ]
                    ],
                ]
            ]
        ];
    }

    public function testReverseTree()
    {
        $this->assertEquals($this->reversedTree, AlgoHelper::reverseTree($this->binaryTree));
    }

    public function testDijkstra()
    {
        $G = [
            array(0,1,2,0,0,0,0),
            array(0,0,0,1,2,0,0),
            array(0,0,0,0,0,2,0),
            array(0,0,0,0,0,1,3),
            array(0,0,0,0,0,0,3),
            array(0,0,0,0,0,0,1),
            array(0,0,0,0,0,0,0),
        ];
        $this->assertEquals([
            1 => 1,
            2 => 2,
            3 => 2,
            4 => 3,
            5 => 3,
            6 => 4,
        ], AlgoHelper::dijkstra($G));
    }
}
