<?php

namespace lb\components\traits\lb;

use FilecacheKit;

trait FileCache
{
    // File Cache Set
    public function fileCacheSet($key, $value, $cache_time = 86400)
    {
        if ($this->isSingle()) {
            FilecacheKit::add($key, $value, $cache_time);
        }
    }

    // File Cache Get
    public function fileCacheGet($key)
    {
        if ($this->isSingle()) {
            return FilecacheKit::get($key);
        }
        return '';
    }

    // File Cache Delete
    public function fileCacheDelete($key)
    {
        if ($this->isSingle()) {
            FilecacheKit::delete($key);
        }
    }

    // File Cache Flush
    public function fileCacheFlush()
    {
        if ($this->isSingle()) {
            FilecacheKit::flush();
        }
    }
}
