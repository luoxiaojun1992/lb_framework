<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午11:10
 * Lb framework mysql db connection file
 */

namespace lb\components\db\mysql;

class Connection
{
    public $conn = false;
    protected $_db = '';
    protected $_host = '';
    protected $_username = '';
    protected $_password = '';
    protected $_options = [];
    protected $_dsn = '';
    public $containers = [];
    protected static $instance = false;

    const DB_TYPE = 'mysql';
    protected $dsn_format = '%s:host=%s;dbname=%s';

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $db_config = $this->containers['config']->get('mysql');
            if ($db_config) {
                $this->_db = isset($db_config['dbname']) ? $db_config['dbname'] : '';
                $this->_host = isset($db_config['host']) ? $db_config['host'] : '';
                $this->_username = isset($db_config['username']) ? $db_config['username'] : '';
                $this->_password = isset($db_config['password']) ? $db_config['password'] : '';
                $this->_options = isset($db_config['options']) ? $db_config['options'] : [];
                $this->getDsn();
                $this->getConnection();
            }
        }
    }

    protected function getDsn()
    {
        $this->_dsn = sprintf($this->dsn_format, self::DB_TYPE, $this->_host, $this->_db);
    }

    protected function getConnection()
    {
        $this->conn = new \PDO($this->_dsn, $this->_username, $this->_password, $this->_options);
    }

    public static function component($containers = [], $reset = false)
    {
        if (self::$instance instanceof self) {
            return $reset ? (self::$instance = new self($containers)) : self::$instance;
        } else {
            return (self::$instance = new self($containers));
        }
    }
}
