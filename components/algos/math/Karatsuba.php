<?php

namespace lb\components\algos\math;

use lb\BaseClass;
use lb\components\error_handlers\ParamException;

/**
 * 大数相乘/递归分治
 *
 * Class Karatsuba
 */
class Karatsuba extends BaseClass
{
    private $num1;

    private $num2;

    /**
     * Karatsuba constructor.
     * @param $num1
     * @param $num2
     * @throws ParamException
     */
    public function __construct($num1, $num2)
    {
        if (!is_numeric($num1) || !is_numeric($num2)) {
            throw new ParamException('num1和num2必须是数字');
        }

        $this->num1 = $num1;
        $this->num2 = $num2;
    }

    public function mul()
    {
        return $this->bigMul($this->num1, $this->num2);
    }

    /**
     * 取数的高位
     *
     * @param $num
     * @param $m
     * @return int
     */
    private function getHighBitPart($num, $m)
    {
        return intval($num / pow(10, $m));
    }

    /**
     * 取数的低位
     *
     * @param $num
     * @param $m
     * @return int
     */
    private function getLowBitPart($num, $m)
    {
        return $num % pow(10, $m);
    }

    /**
     * 获取数字位数
     *
     * @param $num
     * @return int
     */
    private function getBit($num)
    {
        return strlen(abs(intval($num)));
    }

    /**
     * 大数相乘Karatsuba算法
     *
     * @param $num1
     * @param $num2
     * @return float|int|mixed
     */
    private function bigMul($num1, $num2)
    {
        if ($num1 < 10 || $num2 < 10) {
            return $num1 * $num2;
        }

        $m = max([$this->getBit($num1), $this->getBit($num2)]);
        $m2 = floor($m / 2);

        $high1 = $this->getHighBitPart($num1, $m2);
        $low1 = $this->getLowBitPart($num1, $m2);

        $high2 = $this->getHighBitPart($num2, $m2);
        $low2 = $this->getLowBitPart($num2, $m2);

        $z2 = $this->bigMul($high1, $high2);
        $z0 = $this->bigMul($low1, $low2);
        $z1 = $this->bigMul(($high1 + $low1), ($high2 + $low2)) - $z2 - $z0;
        return $z2 * pow(10, 2 * $m2) + $z1 * pow(10, $m2) + $z0;
    }
}
