<?php

namespace lb\components\traits;

use RedisKit;
use lb\Lb;

trait Lock
{
    /**
     * @param $key
     * @param int $ttl
     * @param bool $spinning
     * @return bool|null|string
     */
    public function lock($key = self::class, $ttl = 0, $spinning = false)
    {
        $result = false;
        while (!$result) {
            $result = RedisKit::setnx($this->getLockKey($key), $key, $ttl);
            if (!$spinning) {
                return $result;
            }
        }
        return $result;
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
