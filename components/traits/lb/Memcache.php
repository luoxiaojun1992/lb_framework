<?php

namespace lb\components\traits\lb;

use MemcacheKit;

trait Memcache
{
    /**
     * Memcache Get
     *
     * @param $key
     * @return string
     */
    public function memcacheGet($key)
    {
        if ($this->isSingle()) {
            return MemcacheKit::get($key);
        }
        return '';
    }

    /**
     * Memcache Set
     *
     * @param $key
     * @param $value
     * @param null $expiration
     */
    public function memcacheSet($key, $value, $expiration = null)
    {
        if ($this->isSingle()) {
            MemcacheKit::set($key, $value, $expiration);
        }
    }

    /**
     * Memcache Delete
     *
     * @param $key
     */
    public function memcacheDelete($key)
    {
        if ($this->isSingle()) {
            MemcacheKit::delete($key);
        }
    }
}
