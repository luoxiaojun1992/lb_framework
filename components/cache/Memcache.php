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
    public $containers = [];

    const CACHE_TYPE = 'memcache';

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
            if ($cache_config) {
                $this->_servers = isset($cache_config['servers']) ? $cache_config['servers'] : [];
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
        return $this->conn ? $this->conn->get($key) : '';
    }

    public function set($key, $value, $expiration = null)
    {
        if ($this->conn) {
            $this->conn->add($key, $value, $expiration);
        }
    }

    public function delete($key)
    {
        if ($this->conn) {
            $this->conn->delete($key);
        }
    }
}
