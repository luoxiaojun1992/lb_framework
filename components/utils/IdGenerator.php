<?php

namespace lb\components\utils;

use lb\BaseClass;
use lb\Lb;

class IdGenerator extends BaseClass
{
    static $workerId;
    static $twepoch = 1361775855078;
    static $sequence = 0;
    const workerIdBits = 4;
    static $maxWorkerId = 15;
    const sequenceBits = 10;
    static $workerIdShift = 10;
    static $timestampLeftShift = 14;
    static $sequenceMask = 1023;
    private static $lastTimestamp = -1;

    protected static function timeGen()
    {
        //获得当前时间戳
        $time = explode(' ', microtime());
        $time2= substr($time[0], 2, 3);
        return  $time[1].$time2;
    }

    protected static function tilNextMillis($lastTimestamp)
    {
        $timestamp = static::timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = static::timeGen();
        }

        return $timestamp;
    }

    protected static function nextId()
    {
        $timestamp=static::timeGen();
        if (self::$lastTimestamp == $timestamp) {
            self::$sequence = (self::$sequence + 1) & self::$sequenceMask;
            if (self::$sequence == 0) {
                $timestamp = static::tilNextMillis(self::$lastTimestamp);
            }
        } else {
            self::$sequence  = 0;
        }
        if ($timestamp < self::$lastTimestamp) {
            throw new \Exception("Clock moved backwards.  Refusing to generate id for ".(self::$lastTimestamp-$timestamp)." milliseconds");
        }
        self::$lastTimestamp  = $timestamp;
        return ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::$twepoch)) << self::$timestampLeftShift ) | (self::$workerId << self::$workerIdShift) | self::$sequence;
    }

    public static function generate()
    {
        $id_generator_config = Lb::app()->getIdGeneratorConfig();
        $workId = $id_generator_config['worker_id'] ?? 1;
        if ($workId > self::$maxWorkerId || $workId < 0) {
            throw new \Exception("worker Id can't be greater than 15 or less than 0");
        }
        self::$workerId = $workId;

        return static::nextId();
    }
}
