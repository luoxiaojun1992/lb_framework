<?php

namespace lb\components\token_bucket;

use lb\BaseClass;
use lb\components\traits\Lock;
use lb\components\traits\Singleton;

class Bucket extends BaseClass
{
    use Singleton;
    use Lock;

    const RATE_MARGIN = 0.01;

    const INFINITY_DURATION = 0x7fffffffffffffff;

    protected $startTime;

    protected $capacity;

    protected $quantum;

    protected $fillInterval;

    protected $avail;

    protected $availTick = 0;

    public function __construct($capacity, $quantum, $fillInterval)
    {
        $this->startTime = self::now();
        $this->avail = $this->capacity = $capacity;
        $this->quantum = $quantum;
        $this->fillInterval = $fillInterval;
    }

    /**
     * @param $capacity
     * @param $quantum
     * @param $fillInterval
     * @return Bucket
     */
    public static function component($capacity, $quantum, $fillInterval)
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        } else {
            return (static::$instance = new static($capacity, $quantum, $fillInterval));
        }
    }

    public function wait($count)
    {
        usleep($this->take($count));
    }

    protected static function now()
    {
        return microtime(true) * pow(10, 6);
    }

    protected function take($count, $maxWait = self::INFINITY_DURATION)
    {
        $lockKey = 'token_bucket_rate_limit';

        if (!$this->lock($lockKey, 600, true, 600)) {
            return $maxWait;
        }

        if ($count <= 0) {
            $this->unlock($lockKey);
            return 0;
        }

        $avail = $this->avail - $count;
        if ($avail >= 0) {
            $this->avail = $avail;
            $this->unlock($lockKey);
            return 0;
        }

        $endTick = $this->adjust() + (-$avail + $this->quantum - 1) / $this->quantum;
        $endTime = $this->startTime + (intval($endTick) * $this->fillInterval);
        $waitTime = $endTime - self::now();
        if ($waitTime > $maxWait) {
            $this->unlock($lockKey);
            return 0;
        }

        $this->avail = $avail;
        $this->unlock($lockKey);
        return $waitTime;
    }

    protected function adjust()
    {
        $currentTick = (self::now() - $this->startTime) / $this->fillInterval;
        $currentTick = intval($currentTick);

        if ($this->avail >= $this->capacity) {
            return $currentTick;
        }

        $this->avail += (($currentTick + $this->availTick) * $this->quantum);
        if ($this->avail > $this->capacity) {
            $this->avail = $this->capacity;
        }
        $this->availTick = $currentTick;
        return $currentTick;
    }
}
