<?php

namespace lb\components\algos\data_structure;

use lb\BaseClass;

/**
 * Union find 并查集算法/时间复杂度，接近线性
 *
 * Class Uf
 */
class Uf extends BaseClass
{
    private $id = [];

    private $size = [];

    public function __construct($n)
    {
        $this->id = range(0, $n - 1);
        $cnt = count($this->id);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->size[] = 1;
        }
    }

    /**
     * p和q点是否连通
     *
     * @param $p
     * @param $q
     * @return bool
     */
    public function connected($p, $q)
    {
        //return $this->id[$p] == $this->id[$q];
        return $this->root($p) == $this->root($q);
    }

    /**
     * 时间复杂度，单个O(n)，多个O(n2)
     *
     * @param $p
     * @param $q
     */
    public function union($p, $q)
    {
        /*
        $pid = $this->id[$p];
        $qid = $this->id[$q];
        foreach($this->id as $k => $v) {
            if ($v == $pid) {
                $this->id[$k] = $qid;
            }
        }
        */

        $i = $this->root($p);
        $j = $this->root($q);

        if ($i == $j) {
            return;
        }

        if ($this->size[$i] < $this->size[$j]) {
            $this->id[$i] = $j;
            $this->size[$j] += $this->size[$i];
        } else {
            $this->id[$j] = $i;
            $this->size[$i] += $this->size[$j];
        }
    }

    /**
     * 避免递归
     *
     * @param $i
     * @return mixed
     */
    private function root($i)
    {
        while($i != $this->id[$i]) {
            $this->id[$i] = $this->id[$this->id[$i]]; //path compression
            $i = $this->id[$i];
        }

        return $i;
    }
}
