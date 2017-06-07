<?php

namespace lb\components\traits\lb;

use FilecacheKit;
use MemcacheKit;
use RedisKit;

trait Cache
{
    use FileCache;
    use Memcache;
    use Redis;

    public function setCache($key, $content, $cacheType = FilecacheKit::CACHE_TYPE, $expire = 86400)
    {
        if ($this->isSingle()) {
            switch ($cacheType) {
                case FilecacheKit::CACHE_TYPE:
                    $this->fileCacheSet($key, $content, $expire);
                    break;
                case MemcacheKit::CACHE_TYPE:
                    $this->memcacheSet($key, $content, $expire);
                    break;
                case RedisKit::CACHE_TYPE:
                    $this->redisSet($key, $content, $expire);
                    break;
                default:
                    $this->fileCacheSet($key, $content, $expire);
            }
        }
    }

    public function getCache($key, $cacheType = FilecacheKit::CACHE_TYPE)
    {
        if ($this->isSingle()) {
            switch ($cacheType) {
                case FilecacheKit::CACHE_TYPE:
                    return $this->fileCacheGet($key);
                case MemcacheKit::CACHE_TYPE:
                    return $this->memcacheGet($key);
                case RedisKit::CACHE_TYPE:
                    return $this->redisGet($key);
                default:
                    return $this->fileCacheGet($key);
            }
        }

        return null;
    }
}
