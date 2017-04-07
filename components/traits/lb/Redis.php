<?php

namespace lb\components\traits\lb;

use RedisKit;

trait Redis
{
    /**
     * Redis Get
     *
     * @param $key
     * @return bool|string
     */
    public function redisGet($key)
    {
        if ($this->isSingle()) {
            return RedisKit::get($key);
        }
        return '';
    }

    /**
     * Redis Set
     *
     * @param $key
     * @param $value
     * @param null $expiration
     */
    public function redisSet($key, $value, $expiration = null)
    {
        if ($this->isSingle()) {
            RedisKit::set($key, $value, $expiration);
        }
    }

    /**
     * Redis Delete
     *
     * @param $key
     */
    public function redisDelete($key)
    {
        if ($this->isSingle()) {
            RedisKit::delete($key);
        }
    }
}
