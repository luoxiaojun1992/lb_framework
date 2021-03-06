<?php

namespace lb\components\algos\data_structure;

use lb\BaseClass;

/**
 * 树/深度、广度优先遍历、翻转、左序遍历
 *
 * Class Tree
 *
 * @package lb\components\algos\math
 */
class Tree extends BaseClass
{
    private $tree;

    public function __construct($tree)
    {
        $this->tree = $tree;
    }

    /**
     * 深度优先遍历
     *
     * @param array         $tree
     * @param \Closure|null $callback
     */
    public function depthFirst($tree = [], \Closure $callback = null)
    {
        if (!$tree) {
            $tree = $this->tree;
        }
        foreach ($tree as $node) {
            if ($callback) {
                call_user_func_array($callback, ['node' => $node]);
            }
            $children = $node['children'];
            if ($children) {
                $this->depthFirst($children, $callback);
            }
        }
    }

    /**
     * 广度优先遍历
     *
     * @param array         $tree
     * @param \Closure|null $callback
     */
    public function breadthFirst($tree = [], \Closure $callback = null)
    {
        if (!$tree) {
            $tree = $this->tree;
        }
        $children = [];
        foreach ($tree as $node) {
            if ($callback) {
                call_user_func_array($callback, ['node' => $node]);
            }
            $nodeChildren = $node['children'];
            if ($nodeChildren) {
                $children = array_merge($children, $nodeChildren);
            }
        }
        if ($children) {
            $this->breadthFirst($children);
        }
    }

    /**
     * 翻转
     *
     * @param  array $tree
     * @return array
     */
    public function reverseTree($tree = [])
    {
        if (!$tree) {
            $tree = $this->tree;
        }
        $tempArr = [];
        foreach ($tree as $k => $node) {
            $nodeChild = $node['children'];
            if ($nodeChild) {
                $node['children'] = $this->reverseTree($nodeChild);
            }
            array_unshift($tempArr, $node);
        }
        return $tempArr;
    }

    /**
     * 左序遍历
     *
     * @param array         $tree
     * @param \Closure|null $callback
     */
    public function leftSequence($tree = [], \Closure $callback = null)
    {
        if (!$tree) {
            $tree = $this->tree;
        }

        foreach ($tree as $k => $node) {
            $nodeChild = $node['children'];
            if ($nodeChild) {
                $this->leftSequence([$nodeChild[0]]);
            }
            if ($callback) {
                call_user_func_array($callback, ['node' => $node]);
            }
            if ($nodeChild) {
                $this->leftSequence([$nodeChild[1]]);
            }
        }
    }

    /**
     * 右序遍历
     *
     * @param array         $tree
     * @param \Closure|null $callback
     */
    public function rightSequence($tree = [], \Closure $callback = null)
    {
        if (!$tree) {
            $tree = $this->tree;
        }

        foreach ($tree as $k => $node) {
            $nodeChild = $node['children'];
            if ($nodeChild) {
                $this->rightSequence([$nodeChild[1]]);
            }
            if ($callback) {
                call_user_func_array($callback, ['node' => $node]);
            }
            if ($nodeChild) {
                $this->rightSequence([$nodeChild[0]]);
            }
        }
    }
}
