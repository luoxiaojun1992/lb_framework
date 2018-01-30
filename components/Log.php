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

    /**
     * Defer logs
     *
     * @var array
     */
    protected $deferLogs = [];
    protected $deferLogsCount = 0;

    const MAX_DEFER_LOGS = 10;

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

        Lb::app()->on(
            Event::SHUTDOWN_EVENT, function ($event) {
                Log::component()->flush();
            }
        );
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
     * Add a log
     *
     * @param string $message
     * @param array  $context
     * @param int    $level
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     * @param bool   $defer
     */
    public function record(
        $message = '',
        $context = [],
        $level = Logger::NOTICE,
        $role = 'system',
        $times = 0,
        $ttl = 0,
        $defer = false
    ) {
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

            if ($defer) {
                $this->addDeferLog($role, $level, $message, $context);
                if ($this->deferLogsCount >= self::MAX_DEFER_LOGS) {
                    $this->flush();
                }
                return;
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

    /**
     * Add a defer log
     *
     * @param $role
     * @param $level
     * @param $message
     * @param $context
     */
    protected function addDeferLog($role, $level, $message, $context)
    {
        $this->deferLogs[] = compact('role', 'level', 'message', 'context');
        $this->deferLogsCount++;
    }

    /**
     * Remove a defer log
     *
     * @param $k
     */
    protected function removeDeferLog($k)
    {
        if (!isset($this->deferLogs[$k])) {
            return;
        }

        unset($this->deferLogs[$k]);
        $this->deferLogsCount--;
    }

    /**
     * Flush defer logs
     */
    public function flush()
    {
        foreach ($this->deferLogs as $k => $deferLog) {
            $this->record($deferLog['message'], $deferLog['context'], $deferLog['level'], $deferLog['role']);
            $this->removeDeferLog($k);
        }
    }
}
