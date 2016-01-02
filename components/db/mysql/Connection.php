<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午11:10
 * Lb framework mysql db connection file
 */

namespace lb\components\db\mysql;

use lb\BaseClass;

class Connection extends BaseClass
{
    public $write_conn = false;
    public $read_conn = false;
    protected $_master_db = '';
    protected $_master_host = '';
    protected $_master_username = '';
    protected $_master_password = '';
    protected $_master_options = [];
    protected $_master_dsn = '';
    protected $_slave_db = '';
    protected $_slave_host = '';
    protected $_slave_username = '';
    protected $_slave_password = '';
    protected $_slave_options = [];
    protected $_slave_dsn = '';
    public $containers = [];
    protected static $instance = false;

    const DB_TYPE = 'mysql';
    protected $dsn_format = '%s:host=%s;dbname=%s;charset=utf8';

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $db_config = $this->containers['config']->get('mysql');
            if ($db_config) {
                if (isset($db_config['master'])) {
                    $master_db_config = $db_config['master'];
                    $this->_master_db = isset($master_db_config['dbname']) ? $master_db_config['dbname'] : '';
                    $this->_master_host = isset($master_db_config['host']) ? $master_db_config['host'] : '';
                    $this->_master_username = isset($master_db_config['username']) ? $master_db_config['username'] : '';
                    $this->_master_password = isset($master_db_config['password']) ? $master_db_config['password'] : '';
                    $this->_master_options = isset($master_db_config['options']) ? $master_db_config['options'] : [];
                    $this->getDsn('master');
                    $this->getConnection('master');
                }
                if (isset($db_config['slaves'])) {
                    $slave_config = $db_config['slaves'];
                    $slave_count = count($slave_config);
                    $slave_target_num = mt_rand(0, $slave_count - 1);
                    $slave_db_config = $slave_config[$slave_target_num];
                    $this->_slave_db = isset($slave_db_config['dbname']) ? $slave_db_config['dbname'] : '';
                    $this->_slave_host = isset($slave_db_config['host']) ? $slave_db_config['host'] : '';
                    $this->_slave_username = isset($slave_db_config['username']) ? $slave_db_config['username'] : '';
                    $this->_slave_password = isset($slave_db_config['password']) ? $slave_db_config['password'] : '';
                    $this->_slave_options = isset($slave_db_config['options']) ? $slave_db_config['options'] : [];
                    $this->getDsn('slave');
                    $this->getConnection('slave');
                }
            }
        }
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    protected function getDsn($node_type)
    {
        switch ($node_type) {
            case 'master':
                $this->_master_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_master_host, $this->_master_db);
                break;
            case 'slave':
                $this->_slave_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_slave_host, $this->_slave_db);
                break;
            default:
                $this->_master_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_master_host, $this->_master_db);
        }
    }

    protected function getConnection($node_type)
    {
        switch ($node_type) {
            case 'master':
                $this->write_conn = new \PDO($this->_master_dsn, $this->_master_username, $this->_master_password, $this->_master_options);
                break;
            case 'slave':
                $this->read_conn = new \PDO($this->_slave_dsn, $this->_slave_username, $this->_slave_password, $this->_slave_options);
                break;
            default:
                $this->write_conn = new \PDO($this->_master_dsn, $this->_master_username, $this->_master_password, $this->_master_options);
        }
    }

    /**
     * @param array $containers
     * @param bool $reset
     * @return bool|static
     */
    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers));
        }
    }
}
