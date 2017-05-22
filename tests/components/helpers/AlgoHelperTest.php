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
}
