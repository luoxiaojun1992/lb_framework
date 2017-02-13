<?php

namespace lb\components\traits;

use lb\components\cache\Redis;
use lb\Lb;

trait Lock
{
    public function lock($key = self::class)
    {
        if ($lock = $this->getLock($key)) {
            return $lock;
        }
        if (Redis::component()->setnx($this->getLockKey($key), $key)) {
            return $this->getLock($key);
        }
        return null;
    }

    public function unlock($key = self::class)
    {
        return Lb::app()->redisDelete($this->getLockKey($key));
    }

    protected function getLock($key = self::class)
    {
        if ($lock = Lb::app()->redisGet($this->getLockKey($key))) {
            return $lock;
        }

        return null;
    }

    protected function getLockKey($key = self::class)
    {
        return implode('_', ['redis_lock', $key]);
    }
}
