<?php

namespace lb\components\cache;

use lb\BaseClass;
use lb\components\LoadBalancer;
use lb\components\traits\Singleton;
use lb\Lb;

class Redis extends BaseClass
{
    use Singleton;

    /**
     * @var $conn \Redis 
     */
    public $conn;
    protected $_host = '127.0.0.1';
    protected $_port = 6379;
    protected $_timeout = 0.01;
    protected $_password;
    protected $_database = 0;
    protected $_key_prefix = '';
    public $containers = [];

    const CACHE_TYPE = 'redis';

    private function __construct($containers)
    {
        $this->containers = $containers;
        if ($this->containers['config']) {
            $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
            if ($cache_config) {
                $this->getShardingConnection();
            }
        }
    }

    protected function getShardingConnection($server_hosts = [])
    {
        $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
        if (!$server_hosts) {
            foreach ($cache_config as $key => $config) {
                $server_hosts[$key] = $config['host'];
            }
        }
        if ($server_hosts) {
            // 一致性HASH
            $target_host = LoadBalancer::getTargetHost($server_hosts);
            foreach ($server_hosts as $key => $server_host) {
                if ($server_host == $target_host) {
                    $slave_target_num = $key;
                    $target_cache_config = $cache_config[$slave_target_num];
                    $this->_host = $target_cache_config['host'] ?? $this->_host;
                    $this->_port = $target_cache_config['port'] ?? $this->_port;
                    $this->_timeout = $target_cache_config['timeout'] ?? $this->_timeout;
                    $this->_password = $target_cache_config['password'] ?? $this->_password;
                    $this->_database = $target_cache_config['database'] ?? $this->_database;
                    $this->_key_prefix = $target_cache_config['key_prefix'] ?? $this->_key_prefix;
                    try {
                        $this->getConnection();
                    } catch (\Throwable $e) {
                        unset($server_hosts[$slave_target_num]);
                        $this->getShardingConnection($server_hosts);
                    }
                    break;
                }
            }
        }
    }

    protected function getConnection()
    {
        $this->conn = new \Redis();
        $this->conn->connect($this->_host, $this->_port, $this->_timeout);
        if ($this->_password) {
            $this->conn->auth($this->_password);
        }
        $this->conn->select($this->_database);
    }

    /**
     * @param array $containers
     * @param bool  $reset
     * @return Redis
     */
    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers ? : Lb::app()->containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers ? : Lb::app()->containers));
        }
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->get($key) : '';
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->get($key) : '';
        }
    }

    /**
     * @param $key
     * @param $value
     * @param null  $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = null)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->set($key, $value, $expiration) : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->get($key) : '';
        }
    }

    /**
     * @param $key
     * @param $value
     * @param $ttl
     * @return bool
     */
    public function setnx($key, $value, $ttl = 0)
    {
        $key = $this->getKey($key);

        return $this->execute(
            function (\Redis $redisConn) use ($key, $value, $ttl) {
                if ($redisConn) {
                    $redisConn->multi(\Redis::PIPELINE);
                    $res = $redisConn->setnx($key, $value);
                    if (!$res) {
                        return 0;
                    }
                    $res = $redisConn->expire($key, $ttl);
                    if (!$res) {
                        return 0;
                    }
                    $execResult = $redisConn->exec();
                    return is_array($execResult) ? $execResult[0] : $execResult;
                }

                return 0;
            }
        );
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        if ($this->conn) {
            $key = $this->getKey($key);
            try {
                $this->conn->delete($key);
            } catch (\Throwable $e) {
                self::component($this->containers, true);
                $this->conn->delete($key);
            }
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @param $ttl
     * @return bool
     */
    public function expire($key, $ttl)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->expire($key, $ttl) : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->expire($key, $ttl) : false;
        }
    }

    /**
     * @return null|\Redis
     */
    public function multi()
    {
        try {
            return $this->conn ? $this->conn->multi() : null;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->multi() : null;
        }
    }

    /**
     * @return bool
     */
    public function exec()
    {
        try {
            return $this->conn ? $this->conn->exec() : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->exec() : false;
        }
    }

    /**
     * @return bool
     */
    public function discard()
    {
        try {
            return $this->conn ? $this->conn->discard() : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->discard() : false;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function watch($key)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->watch($key) : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->watch($key) : false;
        }
    }

    /**
     * @param $key
     * @return int
     */
    public function scard($key)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->sCard($key) : 0;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->sCard($key) : 0;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function sismember($key, $value)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->sIsMember($key, $value) : false;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->sIsMember($key, $value) : false;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return int
     */
    public function sadd($key, $value)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->sAdd($key, $value) : 0;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->sAdd($key, $value) : 0;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return int
     */
    public function rpush($key, $value)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->rPush($key, $value) : 0;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->rPush($key, $value) : 0;
        }
    }

    /**
     * @param $key
     * @param $start
     * @param $end
     * @return array
     */
    public function zrange($key, $start, $end)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->zRange($key, $start, $end) : [];
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->zRange($key, $start, $end) : [];
        }
    }

    /**
     * @param $key
     * @param $member
     * @return int
     */
    public function zrem($key, $member)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->zRem($key, $member) : 0;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->zRem($key, $member) : 0;
        }
    }

    /**
     * @param $key
     * @return null|string
     */
    public function lpop($key)
    {
        $key = $this->getKey($key);
        try {
            return $this->conn ? $this->conn->lPop($key) : null;
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return $this->conn ? $this->conn->lPop($key) : null;
        }
    }

    /**
     * @param $key
     * @param int $step
     * @return mixed
     */
    public function incr($key, $step = 1)
    {
        $key = $this->getKey($key);

        return $this->execute(
            function ($redisConn) use ($key, $step) {
                return $redisConn ? $redisConn->incrBy($key, $step) : 0;
            }
        );
    }

    /**
     * @param $key
     * @return mixed
     */
    public function exists($key)
    {
        $key = $this->getKey($key);

        return $this->execute(
            function ($redisConn) use ($key) {
                return $redisConn ? $redisConn->exists($key) : false;
            }
        );
    }

    /**
     * @param $callback
     * @return mixed
     */
    protected function execute($callback)
    {
        $redisConn = $this->conn;
        $handler = function () use ($callback, $redisConn) {
            return call_user_func_array($callback, ['redisConn' => $redisConn]);
        };

        try {
            return call_user_func($handler);
        } catch (\Throwable $e) {
            self::component($this->containers, true);
            return call_user_func($handler);
        }
    }

    /**
     * @param $key
     * @return string
     */
    protected function getKey($key)
    {
        if (stripos($key, $this->_key_prefix) !== 0) {
            $key = $this->_key_prefix . $key;
        }
        return $key;
    }
}
