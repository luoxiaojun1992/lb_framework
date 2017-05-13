<?php

namespace lb\components\traits;

use RedisKit;
use lb\Lb;

trait Lock
{
    /**
     * @param $key
     * @param int $ttl
     * @return bool|null|string
     */
    public function lock($key = self::class, $ttl = 0)
    {
        // Lock is occupied
        if ($this->getLock($key)) {
            return false;
        }
        return RedisKit::setnx($this->getLockKey($key), $key, $ttl);
    }

    /**
     * @param $key
     */
    public function unlock($key = self::class)
    {
        Lb::app()->redisDelete($this->getLockKey($key));
    }

    /**
     * @param $key
     * @return bool|string
     */
    protected function getLock($key = self::class)
    {
        return Lb::app()->redisGet($this->getLockKey($key));
    }

    /**
     * @param $key
     * @return string
     */
    protected function getLockKey($key = self::class)
    {
        return implode('_', ['redis_lock', $key]);
    }
}
