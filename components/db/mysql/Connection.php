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
use lb\components\distribution\FlexiHash;
use lb\components\LoadBalancer;
use lb\Lb;

class Connection extends BaseClass
{
    const CONN_TYPE_MASTER = 'master';
    const CONN_TYPE_SLAVE = 'slave';

    /**
     * @var  \PDO 
     */
    public $write_conn;
    /**
     * @var  \PDO 
     */
    public $read_conn;
    protected $_master_db;
    protected $_master_host;
    protected $_master_username;
    protected $_master_password;
    protected $_master_options = [];
    protected $_master_dsn;
    protected $_slave_db;
    protected $_slave_host;
    protected $_slave_username;
    protected $_slave_password;
    protected $_slave_options;
    protected $_slave_dsn;

    /**
     * @var \PDO[]
     */
    public $extraConns = [];
    protected $extraConfigs = [];
    protected $extraDsns = [];

    public $containers = [];

    /**
     * @var Connection
     */
    protected static $instance;

    const DB_TYPE = 'mysql';
    protected $dsn_format = '%s:host=%s;dbname=%s;charset=utf8';

    /**
     * Connection constructor.
     *
     * @param $containers
     */
    public function __construct($containers)
    {
        $this->containers = $containers;
        if ($this->containers['config']) {
            $dbConfigs = $this->containers['config']->get('mysql');
            if (is_array($dbConfigs)) {
                foreach ($dbConfigs as $conn => $dbConfig) {
                    if ($conn == self::CONN_TYPE_MASTER) {
                        $this->getMasterConnection();
                    } elseif ($conn == 'slaves') {
                        $this->getSlaveConnection();
                    } else {
                        $this->getExtraConnection($conn);
                    }
                }
            }
        }
    }

    public function __clone()
    {
        //
    }

    /**
     * Establish master node mysql connection
     */
    protected function getMasterConnection()
    {
        $db_config = $this->containers['config']->get('mysql');
        $master_db_config = $db_config['master'];
        $this->_master_db = isset($master_db_config['dbname']) ? $master_db_config['dbname'] : '';
        $this->_master_host = isset($master_db_config['host']) ? $master_db_config['host'] : '';
        $this->_master_username = isset($master_db_config['username']) ? $master_db_config['username'] : '';
        $this->_master_password = isset($master_db_config['password']) ? $master_db_config['password'] : '';
        $this->_master_options = isset($master_db_config['options']) ? $master_db_config['options'] : [];
        $this->getDsn(self::CONN_TYPE_MASTER);
        $this->getConnection(self::CONN_TYPE_MASTER);
    }

    /**
     * Establish slave node mysql connection
     *
     * @param array $server_hosts
     */
    protected function getSlaveConnection($server_hosts = [])
    {
        $db_config = $this->containers['config']->get('mysql');
        $slave_config = $db_config['slaves'];
        if (!$server_hosts) {
            foreach ($slave_config as $key => $config) {
                $server_hosts[$key] = $config['host'];
            }
        }
        if ($server_hosts) {
            // 一致性HASH
            $target_host = LoadBalancer::getTargetHost($server_hosts);
            foreach ($server_hosts as $key => $server_host) {
                if ($server_host == $target_host) {
                    $slave_target_num = $key;
                    $target_slave_config = $slave_config[$slave_target_num];
                    $this->_slave_db = $target_slave_config['dbname'] ?? '';
                    $this->_slave_host = $target_slave_config['host'] ?? '';
                    $this->_slave_username = $target_slave_config['username'] ?? '';
                    $this->_slave_password = $target_slave_config['password'] ?? '';
                    $this->_slave_options = $target_slave_config['options'] ?? [];
                    $this->getDsn(self::CONN_TYPE_SLAVE);
                    try {
                        $this->getConnection(self::CONN_TYPE_SLAVE);
                    } catch (\PDOException $e) {
                        unset($server_hosts[$slave_target_num]);
                        $this->getSlaveConnection($server_hosts);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Establish extra mysql connection
     *
     * @param $conn
     */
    protected function getExtraConnection($conn)
    {
        $dbConfigs = $this->containers['config']->get('mysql');
        $dbConfig = $dbConfigs[$conn];
        $this->extraConfigs[$conn]['_db'] = $dbConfig['dbname'] ?? '';
        $this->extraConfigs[$conn]['_host'] = $dbConfig['host'] ?? '';
        $this->extraConfigs[$conn]['_username'] = $dbConfig['username'] ?? '';
        $this->extraConfigs[$conn]['_password'] = $dbConfig['password'] ?? '';
        $this->extraConfigs[$conn]['_options'] = $dbConfig['options'] ?? '';
        $this->getDsn($conn);
        $this->getConnection($conn);
    }

    /**
     * Generate dsn
     *
     * @param $node_type
     */
    protected function getDsn($node_type)
    {
        switch ($node_type) {
        case self::CONN_TYPE_MASTER:
            $this->_master_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_master_host, $this->_master_db);
            break;
        case self::CONN_TYPE_SLAVE:
            $this->_slave_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_slave_host, $this->_slave_db);
            break;
        default:
            if (in_array($node_type, $this->extraConfigs)) {
                $extraConfig = $this->extraConfigs[$node_type];
                $this->extraDsns[$node_type] = sprintf($this->dsn_format, static::DB_TYPE, $extraConfig['_host'], $extraConfig['_db']);
            } else {
                $this->_master_dsn = sprintf($this->dsn_format, static::DB_TYPE, $this->_master_host, $this->_master_db);
            }
        }
    }

    /**
     * Establish mysql connection
     *
     * @param $node_type
     */
    protected function getConnection($node_type)
    {
        switch ($node_type) {
        case self::CONN_TYPE_MASTER:
            $this->write_conn = new \PDO($this->_master_dsn, $this->_master_username, $this->_master_password, $this->_master_options);
            break;
        case self::CONN_TYPE_SLAVE:
            $this->read_conn = new \PDO($this->_slave_dsn, $this->_slave_username, $this->_slave_password, $this->_slave_options);
            break;
        default:
            if (in_array($node_type, $this->extraConfigs) && in_array($node_type, $this->extraDsns)) {
                $extraConfig = $this->extraConfigs[$node_type];
                $this->extraConns[$node_type] = new \PDO($this->extraDsns[$node_type], $extraConfig['_username'], $extraConfig['_password'], $extraConfig['_options']);
            } else {
                $this->write_conn = new \PDO($this->_master_dsn, $this->_master_username, $this->_master_password, $this->_master_options);
            }
        }
    }

    /**
     * @param array $containers
     * @param bool  $reset
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
