<?php

namespace lb\components\traits\lb;

use MemcacheKit;

trait Memcache
{
    // Memcache Get
    public function memcacheGet($key)
    {
        if ($this->isSingle()) {
            return MemcacheKit::get($key);
        }
        return '';
    }

    // Memcache Set
    public function memcacheSet($key, $value, $expiration = null)
    {
        if ($this->isSingle()) {
            MemcacheKit::set($key, $value, $expiration);
        }
    }

    // Memcache Delete
    public function memcacheDelete($key)
    {
        if ($this->isSingle()) {
            MemcacheKit::delete($key);
        }
    }
}
