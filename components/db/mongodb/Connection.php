<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午11:10
 * Lb framework mysql db connection file
 */

namespace lb\components\db\mongodb;

use lb\BaseClass;
use lb\Lb;

class Connection extends BaseClass
{
    public $_conn;
    protected $_db;
    protected $_host;
    protected $_username;
    protected $_password;
    protected $_dsn;
    public $containers = [];
    protected static $instance;

    const DB_TYPE = 'mongodb';
    protected $dsn_format = '%s://%s%s/%s';

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $db_config = $this->containers['config']->get('mongodb');
            if ($db_config) {
                $this->_db = isset($db_config['dbname']) ? $db_config['dbname'] : '';
                $this->_host = isset($db_config['host']) ? $db_config['host'] : '';
                $this->_username = isset($db_config['username']) ? $db_config['username'] : '';
                $this->_password = isset($db_config['password']) ? $db_config['password'] : '';
                $this->getDsn();
                $this->getConnection();
            }
        }
    }

    public function __clone()
    {
        //
    }

    protected function getDsn()
    {
        if ($this->_username && $this->_password) {
            $auth_str = implode(':', [$this->_username, $this->_password]) . '@';
        } else {
            $auth_str = '';
        }
        $this->_dsn = sprintf($this->dsn_format, static::DB_TYPE, $auth_str, $this->_host, $this->_db);
    }

    protected function getConnection()
    {
        $this->_conn = new \MongoDB\Driver\Manager($this->_dsn);
    }

    /**
     * @param array $containers
     * @param bool $reset
     * @return Connection
     */
    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers ? : Lb::app()->containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers ? : Lb::app()->containers));
        }
    }
}
