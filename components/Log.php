<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/18
 * Time: 9:38
 * Lb framework log component file
 */

namespace lb\components;

use lb\Lb;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    protected $loggers = [];
    protected static $instance = false;

    public function __construct()
    {
        $system_logger = new Logger('system');
        $system_logger->pushHandler(new StreamHandler(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR .'system' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', Logger::NOTICE));
        $this->loggers['system'] = $system_logger;

        $user_logger = new Logger('user');
        $user_logger->pushHandler(new StreamHandler(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR .'user' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', Logger::NOTICE));
        $this->loggers['user'] = $user_logger;
    }

    public static function component()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return (self::$instance = new self());
        }
    }

    public function log($role = 'system', $level = Logger::NOTICE, $message = '', $context = [])
    {
        if (isset($this->loggers[$role])) {
            $this->loggers[$role]->addRecord($level, $message, $context);
        }
    }
}
