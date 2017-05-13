<?php

namespace lb\components\utils;

use lb\BaseClass;
use lb\components\traits\Lock;
use lb\components\traits\Singleton;
use lb\Lb;

class IdGenerator extends BaseClass
{
    use Lock;
    use Singleton;

    private $workerId;
    private $twepoch = 1361775855078;
    private $sequence = 0;
    private $maxWorkerId = 15;
    private $workerIdShift = 10;
    private $timestampLeftShift = 14;
    private $sequenceMask = 1023;
    private $lastTimestamp = -1;

    /**
     * @return string
     */
    protected function timeGen()
    {
        //获得当前时间戳
        $time = explode(' ', microtime());
        $time2= substr($time[0], 2, 3);
        return  $time[1].$time2;
    }

    /**
     * @param $lastTimestamp
     * @return string
     */
    protected function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function nextId()
    {
        $timestamp = $this->timeGen();
        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & $this->sequenceMask;
            if ($this->sequence == 0) {
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence  = 0;
        }
        if ($timestamp < $this->lastTimestamp) {
            throw new \Exception("Clock moved backwards.  Refusing to generate id for " . ($this->lastTimestamp-$timestamp) . " milliseconds");
        }
        $this->lastTimestamp  = $timestamp;
        return ((sprintf('%.0f', $timestamp) - sprintf('%.0f', $this->twepoch)) << $this->timestampLeftShift ) | ($this->workerId << $this->workerIdShift) | $this->sequence;
    }

    /**
     * @param string $prefix
     * @return int
     * @throws \Exception
     */
    public function generate($prefix = '')
    {
        $id_generator_config = Lb::app()->getIdGeneratorConfig();
        $workId = $id_generator_config['worker_id'] ?? 1;
        if ($workId > $this->maxWorkerId || $workId < 0) {
            throw new \Exception("worker Id can't be greater than 15 or less than 0");
        }
        $this->workerId = $workId;

        $nextId = $prefix . $this->nextId();
        $lock_key = ($prefix ? $prefix . '@' . self::class : self::class) . $nextId;

        //After 5 minutes lock will be released
        while (!$this->lock($lock_key, 300)) {
            $nextId = $prefix . $this->nextId();
            $lock_key = ($prefix ? $prefix . '@' . self::class : self::class) . $nextId;
        }

        return $nextId;
    }
}
