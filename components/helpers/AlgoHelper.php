<?php

namespace lb\components\helpers;

use lb\BaseClass;

class AlgoHelper extends BaseClass
{
    public static function depthFirst($binaryTree)
    {
        foreach ($binaryTree as $node) {
            var_dump($node['value']);
            $children = $node['children'];
            if ($children) {
                self::depthFirst($children);
            }
        }
    }

    public static function breadthFirst($binaryTree)
    {
        $children = [];
        foreach ($binaryTree as $node) {
            var_dump($node['value']);
            $nodeChild = $node['children'];
            if ($nodeChild) {
                $children = array_merge($children, $nodeChild);
            }
        }
        if ($children) {
            self::breadthFirst($children);
        }
    }

    public static function reverseTree($binaryTree)
    {
        $tempArr = [];
        foreach ($binaryTree as $k => $node) {
            $nodeChild = $node['children'];
            if ($nodeChild) {
                $node['children'] = self::reverseTree($nodeChild);
            }
            array_unshift($tempArr, $node);
        }
        return $tempArr;
    }
}
