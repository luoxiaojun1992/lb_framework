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
            $cache_config = $this->containers['config']->get(self::CACHE_TYPE);
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

    public static function component($containers = [], $reset = false)
    {
        if (self::$instance instanceof self) {
            return $reset ? (self::$instance = new self($containers)) : self::$instance;
        } else {
            return (self::$instance = new self($containers));
        }
    }

    public function get($key)
    {
        return $this->conn->get($key);
    }

    public function set($key, $value, $expiration = null)
    {
        $this->conn->add($key, $value, $expiration);
    }

    public function delete($key)
    {
        $this->conn->delete($key);
    }
}
