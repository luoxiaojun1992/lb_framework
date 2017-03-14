<?php

namespace lb\components\traits;

use RedisKit;
use lb\Lb;

trait Lock
{
    public function lock($key = self::class)
    {
        if ($lock = $this->getLock($key)) {
            return $lock;
        }
        if (RedisKit::setnx($this->getLockKey($key), $key)) {
            return $this->getLock($key);
        }
        return null;
    }

    public function unlock($key = self::class)
    {
        Lb::app()->redisDelete($this->getLockKey($key));
    }

    protected function getLock($key = self::class)
    {
        return Lb::app()->redisGet($this->getLockKey($key));
    }

    protected function getLockKey($key = self::class)
    {
        return implode('_', ['redis_lock', $key]);
    }
}
