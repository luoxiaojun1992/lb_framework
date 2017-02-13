<?php

namespace lb\components\cache;

use lb\BaseClass;
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
    protected $_password = null;
    protected $_database = 0;
    public $containers = [];

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
