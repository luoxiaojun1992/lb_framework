<?php

namespace lb\components\traits\lb;

use Monolog\Logger;
use lb\components\Log as LogComponent;

trait Log
{
    /**
     * Record log
     *
     * @param string $message
     * @param array  $context
     * @param int    $level
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function log($message = '', $context = [], $level = Logger::NOTICE, $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            LogComponent::component()->record($message, $context, $level, $role, $times, $ttl);
        }
    }

    /**
     * Record debug log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function debug($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::DEBUG, $role, $times, $ttl);
        }
    }

    /**
     * Record info log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function info($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::INFO, $role, $times, $ttl);
        }
    }

    /**
     * Record notice log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function notice($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::NOTICE, $role, $times, $ttl);
        }
    }

    /**
     * Record warning log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function warning($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::WARNING, $role, $times, $ttl);
        }
    }

    /**
     * Record error log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function error($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::ERROR, $role, $times, $ttl);
        }
    }

    /**
     * Record critical log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function critical($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::CRITICAL, $role, $times, $ttl);
        }
    }

    /**
     * Record alert log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function alert($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::ALERT, $role, $times, $ttl);
        }
    }

    /**
     * Record emergency log
     *
     * @param string $message
     * @param array  $context
     * @param string $role
     * @param int    $times
     * @param int    $ttl
     */
    public function emergency($message = '', $context = [], $role = 'system', $times = 0, $ttl = 0)
    {
        if ($this->isSingle()) {
            $this->log($message, $context, Logger::EMERGENCY, $role, $times, $ttl);
        }
    }
}
