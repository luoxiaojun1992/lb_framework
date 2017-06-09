<?php

namespace lb\components\helpers;

use lb\BaseClass;

class AlgoHelper extends BaseClass
{
    public static function depthFirst($binaryTree, \Closure $callback = null)
    {
        foreach ($binaryTree as $node) {
            call_user_func_array($callback, ['node' => $node]);
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

    public static function dijkstra($G, &$d, $startNode = 0)
    {
        $totalNode = count($G);

        //存储已经选择节点和剩余节点
        $U = [$startNode];
        $V = [];
        for ($j = 0; $j < $totalNode; ++$j) {
            if ($j == $startNode) {
                continue;
            }
            $V[] = $j;
            yield;
        }

        //存储路径上节点距离源点的最小距离
        $d = [];

        //初始化图中节点与源点的最小距离
        for ($i = 0; $i < $totalNode; $i++) {
            if ($i == $startNode) {
                continue;
            }

            if ($G[$startNode][$i] > 0) {
                $d[$i] = $G[$startNode][$i];
            } else {
                $d[$i] = 1000000;
            }
            yield;
        }

        //n-1次循环完成转移节点任务
        for ($l = 0; $l < ($totalNode - 1); $l++) {
            //查找剩余节点中距离源点最近的节点v
            $current_min = 100000;
            $current_min_v = $startNode;
            foreach ($V as $k => $v) {
                if($d[$v] < $current_min) {
                    $current_min = $d[$v];
                    $current_min_v = $v;
                }
                yield;
            }

            //从V中更新顶点到U中
            array_push($U,$current_min_v);
            array_splice($V,array_search($current_min_v,$V),1);

            //更新
            foreach($V as $k => $u) {
                if ($G[$current_min_v][$u] != 0 && $d[$u] > $d[$current_min_v] + $G[$current_min_v][$u]) {
                    $d[$u] = $d[$current_min_v] + $G[$current_min_v][$u];
                }
                yield;
            }

            yield;
        }
    }
}
