<?php

namespace lb\components\traits\lb;

use RedisKit;

trait Redis
{
    // Redis Get
    public function redisGet($key)
    {
        if ($this->isSingle()) {
            return RedisKit::get($key);
        }
        return '';
    }

    // Redis Set
    public function redisSet($key, $value, $expiration = null)
    {
        if ($this->isSingle()) {
            RedisKit::set($key, $value, $expiration);
        }
    }

    // Redis Delete
    public function redisDelete($key)
    {
        if ($this->isSingle()) {
            RedisKit::delete($key);
        }
    }
}
