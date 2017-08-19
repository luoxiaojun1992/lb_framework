<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\consts\Event;
use lb\components\db\mysql\Connection;
use lb\components\events\LogWriteEvent;
use lb\components\log_handlers\PDOHandler;
use lb\components\log_handlers\QueueHandler;
use lb\components\traits\Singleton;
use lb\Lb;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RedisKit;

class Log extends BaseClass implements Event
{
    use Singleton;

    protected $loggers = [];

    //Log Handler Types
    const LOG_TYPE_MYSQL = 'mysql';
    const LOG_TYPE_QUEUE = 'queue';

    public function __construct()
    {
        $handler = new StreamHandler(
            Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR .
            'log' . DIRECTORY_SEPARATOR .'system' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', Logger::NOTICE
        );
        $log_config = Lb::app()->getLogConfig();
        if (!empty($log_config['type'])) {
            switch($log_config['type']) {
            case self::LOG_TYPE_MYSQL:
                $handler = new PDOHandler(Connection::component()->write_conn, Logger::NOTICE);
                break;
            case self::LOG_TYPE_QUEUE:
                $handler = new QueueHandler(Logger::NOTICE);
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

    /**
     * @return Log
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        } else {
            return (static::$instance = new static());
        }
    }

    /**
     * @param string $message
     * @param array  $context
     * @param $level
     * @param string $role
     * @param $times
     * @param $ttl
     */
    public function record($message = '', $context = [], $level = Logger::NOTICE, $role = 'system', $times = 0, $ttl = 0)
    {
        if (isset($this->loggers[$role])) {
            if ($times > 0 && $ttl > 0) {
                $cacheKey = md5($message);
                $cnt = RedisKit::incr($cacheKey);
                if ($cnt == 1) {
                    RedisKit::expire($cacheKey, $ttl);
                }
                if ($cnt > $times) {
                    return;
                }
            }

            $this->loggers[$role]->addRecord($level, $message, $context);
            Lb::app()->trigger(
                self::LOG_WRITE_EVENT, new LogWriteEvent(
                    [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context,
                    'role' => $role,
                    'time' => time(),
                    ]
                )
            );
        }
    }
}
