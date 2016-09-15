<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/18
 * Time: 9:38
 * Lb framework log component file
 */

namespace lb\components;

use lb\BaseClass;
use lb\components\db\mysql\Connection;
use lb\components\log_handlers\PDOHandler;
use lb\Lb;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log extends BaseClass
{
    protected $loggers = [];
    protected static $instance = false;

    public function __construct()
    {
        $handler = new StreamHandler(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR .'system' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', Logger::NOTICE);
        $log_config = Lb::app()->getLogConfig();
        if ($log_config && isset($log_config['type'])) {
            switch($log_config['type']) {
                case 'mysql':
                    $handler = new PDOHandler(Connection::component()->write_conn, Logger::NOTICE);
                    break;
            }
        }

        $system_logger = new Logger('system');
        $system_logger->pushHandler($handler);
        $this->loggers['system'] = $system_logger;

        $user_logger = new Logger('user');
        $user_logger->pushHandler($handler);
        $this->loggers['user'] = $user_logger;
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function component()
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        } else {
            return (static::$instance = new static());
        }
    }

    public function record($role = 'system', $level = Logger::NOTICE, $message = '', $context = [])
    {
        if (isset($this->loggers[$role])) {
            $this->loggers[$role]->addRecord($level, $message, $context);
        }
    }
}
