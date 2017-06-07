<?php

namespace lb\components\db\mysql;

use lb\components\helpers\JsonHelper;
use lb\Lb;
use FilecacheKit;

trait Cache
{
    public function getCache($args)
    {
        $mysqlCacheConfig = Lb::app()->getMysqlCacheConfig();
        return JsonHelper::decode(
            Lb::app()->getCache($this->getCacheKey($args), $mysqlCacheConfig['cache_type'] ?? FilecacheKit::CACHE_TYPE)
        );
    }

    public function setCache($args, $result, $expire = null)
    {
        $mysqlCacheConfig = Lb::app()->getMysqlCacheConfig();
        Lb::app()->setCache(
            $this->getCacheKey($args),
            JsonHelper::encode($result),
            $mysqlCacheConfig['cache_type'] ?? FilecacheKit::CACHE_TYPE,
            $expire ? : ($mysqlCacheConfig['expire'] ?? 86400)
        );
    }

    protected function getCacheKey($args)
    {
        return JsonHelper::encode($args);
    }
}
