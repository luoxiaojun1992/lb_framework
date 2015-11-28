<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/10
 * Time: 15:12
 * Lb framework memcache cache component file
 */

namespace lb\components\cache;

class Memcache
{
    public $conn = false;
    protected $_servers = [];
    public $containers = [];
    protected static $instance = false;

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

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    protected function getConnection()
    {
        $this->conn = new \Memcached();
        $this->conn->addServers($this->_servers);
    }

    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers));
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
