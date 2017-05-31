<?php

namespace lb\components\cache;

use lb\BaseClass;
use lb\components\traits\Singleton;
use lb\Lb;

class Memcache extends BaseClass
{
    use Singleton;

    public $conn = false;
    protected $_servers = [];
    protected $_key_prefix = '';
    public $containers = [];

    const CACHE_TYPE = 'memcache';

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
            if ($cache_config) {
                $this->_servers = isset($cache_config['servers']) ? $cache_config['servers'] : [];
                $this->_key_prefix = isset($cache_config['key_prefix']) ? $cache_config['key_prefix'] : '';
                $this->getConnection();
            }
        }
    }

    protected function getConnection()
    {
        $this->conn = new \Memcached();
        $this->conn->addServers($this->_servers);
    }

    /**
     * @param array $containers
     * @param bool $reset
     * @return Memcache
     */
    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers ? : Lb::app()->containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers ? : Lb::app()->containers));
        }
    }

    public function get($key)
    {
        $this->getKey($key);
        return $this->conn ? $this->conn->get($key) : '';
    }

    public function set($key, $value, $expiration = null)
    {
        if ($this->conn) {
            $this->getKey($key);
            $this->conn->add($key, $value, $expiration);
        }
    }

    public function delete($key)
    {
        if ($this->conn) {
            $this->getKey($key);
            $this->conn->delete($key);
        }
    }

    protected function getKey(&$key)
    {
        if (stripos($key, $this->_key_prefix) !== 0) {
            $key = $this->_key_prefix . $key;
        }
        return $key;
    }
}
