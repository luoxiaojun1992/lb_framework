<?php

namespace lb\components\traits;

use RedisKit;
use lb\Lb;

class RateLimit
{
    public function getRateByKey($key = self::class)
    {
        return Lb::app()->redisGet($this->getRateLimitKey($key));
    }

    public function isOverRate($rate, $key = self::class)
    {
        return intval($this->getRateByKey($key)) > intval($rate);
    }

    public function setRate($expire = 0, $step = 1, $key = self::class)
    {
        $rateLimitKey = $this->getRateLimitKey($key);

        if ($step > 1) {
            RedisKit::incrBy($rateLimitKey, $step);
        } else {
            RedisKit::incr($rateLimitKey);
        }

        $expire > 0 && RedisKit::expire($rateLimitKey);
    }

    protected function getRateLimitKey($key = self::class)
    {
        return implode('_', ['redis_rate_limit', $key]);
    }
}
