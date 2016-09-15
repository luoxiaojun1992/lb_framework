<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/12
 * Time: 15:26
 * Lb framework redis cache component file
 */

namespace lb\components\cache;

use lb\BaseClass;

class Redis extends BaseClass
{
    public $conn = false;
    protected $_host = '127.0.0.1';
    protected $_port = 6379;
    protected $_timeout = 0.01;
    protected $_password = null;
    protected $_database = 0;
    public $containers = [];
    protected static $instance = false;

    const CACHE_TYPE = 'redis';

    private function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
            if ($cache_config) {
                $this->_host = isset($cache_config['host']) ? $cache_config['host'] : $this->_host;
                $this->_port = isset($cache_config['port']) ? $cache_config['port'] : $this->_port;
                $this->_timeout = isset($cache_config['timeout']) ? $cache_config['timeout'] : $this->_timeout;
                $this->_password = isset($cache_config['password']) ? $cache_config['password'] : $this->_password;
                $this->_database = isset($cache_config['database']) ? $cache_config['database'] : $this->_database;
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
        $this->conn = new \Redis();
        $this->conn->connect($this->_host, $this->_port, $this->_timeout);
        if ($this->_password) {
            $this->conn->auth($this->_password);
        }
        $this->conn->select($this->_database);
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

    public function set($key, $value, $expiration = 0)
    {
        if ($this->conn) {
            $this->conn->set($key, $value, $expiration);
        }
    }

    public function delete($key)
    {
        if ($this->conn) {
            $this->conn->delete($key);
        }
    }
}
