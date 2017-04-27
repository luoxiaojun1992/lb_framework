<?php

namespace lb\components\cache;

use lb\BaseClass;
use lb\components\distribution\FlexiHash;
use lb\components\traits\Singleton;
use lb\Lb;

class Redis extends BaseClass
{
    use Singleton;

    /** @var $conn \Redis */
    public $conn;
    protected $_host = '127.0.0.1';
    protected $_port = 6379;
    protected $_timeout = 0.01;
    protected $_password;
    protected $_database = 0;
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
            $target_host = FlexiHash::component()->addServers($server_hosts)->lookup();
            foreach ($server_hosts as $key => $server_host) {
                if ($server_host == $target_host) {
                    $slave_target_num = $key;
                    $target_cache_config = $cache_config[$slave_target_num];
                    $this->_host = $target_cache_config['host'] ?? $this->_host;
                    $this->_port = $target_cache_config['port'] ?? $this->_port;
                    $this->_timeout = $target_cache_config['timeout'] ?? $this->_timeout;
                    $this->_password = $target_cache_config['password'] ?? $this->_password;
                    $this->_database = $target_cache_config['database'] ?? $this->_database;
                    try {
                        $this->getConnection();
                    } catch (\Exception $e) {
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
     * @param bool $reset
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
        return $this->conn ? $this->conn->get($key) : '';
    }

    /**
     * @param $key
     * @param $value
     * @param null $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = null)
    {
        if ($this->conn) {
            return $this->conn->set($key, $value, $expiration);
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function setnx($key, $value)
    {
        if ($this->conn) {
            return $this->conn->setnx($key, $value);
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        if ($this->conn) {
            $this->conn->delete($key);
            return true;
        }

        return false;
    }
}
