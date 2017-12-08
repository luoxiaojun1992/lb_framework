<?php

namespace lb\components\algos\math;

use lb\BaseClass;

/**
 * 数字转中文
 *
 * Class Num2Chinese
 */
class Num2Chinese extends BaseClass
{
    //中文数字
    const CHINESE_NUM_CHAR = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];

    //中文数字分段
    const CHINESE_UNIT_SECTION = ['', '万', '亿', '万亿'];

    //中文数字单位
    const CHINESE_UNIT_CHAR = ['', '十', '百', '千'];

    //待转换的数字
    private $num;

    /**
     * Num2Chinese constructor.
     * @param $num
     */
    public function __construct($num)
    {
        $this->num = $num;
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
     * 数字转中文
     *
     * @param bool $simple
     * @return string
     */
    public function number2Chinese($simple = true)
    {
        $str = '';
        $unitPos = 0;

        while ($this->num > 0) {
            //分段字符串
            $sectionStr = '';

            //取最低四位
            $section = $this->getLowBitPart($this->num, 4);

            //获取段中文字符串
            $this->section2Chinese($section, $sectionStr);

            //拼接段数字单位
            $sectionStr .= ($section != 0) ? self::CHINESE_UNIT_SECTION[$unitPos] : self::CHINESE_UNIT_SECTION[0];

            $this->num = intval($this->num / 10000);
            ++$unitPos;

            //补零
            if (($section < 1000) && ($section > 0) && $this->num > 0) {
                $sectionStr = self::CHINESE_NUM_CHAR[0] . $sectionStr;
            }

            $str = $sectionStr . $str;
        }

        return $str;
    }

    /**
     * 分段数字转中文
     *
     * @param $section
     * @param $str
     * @param bool $simple
     */
    private function section2Chinese($section, &$str, $simple = true)
    {
        $unitPos = 0;

        while ($section > 0) {
            //每一位数字字符串
            $vStr = '';

            //取最低一位
            $v = $this->getLowBitPart($section, 1);

            //拼接数字中文和单位
            $vStr .= self::CHINESE_NUM_CHAR[$v];
            $vStr .= ($v != 0) ? self::CHINESE_UNIT_CHAR[$unitPos] : self::CHINESE_UNIT_CHAR[0];

            ++$unitPos;
            $section = intval($section / 10);

            $str = $vStr . $str;
        }
    }
}
