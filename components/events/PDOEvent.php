<?php

namespace lb\components\events;

class PDOEvent extends BaseEvent
{
    public $statement;
    public $duration;
    public $pdoStatement;
    public $bindings;
    public $memory;
    public $startTime;
    public $endTime;
    public $startMemory;
    public $endMemory;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param $statement
     * @return $this
     */
    public function setStatement($statement)
    {
        $this->statement = $statement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPdoStatement()
    {
        return $this->pdoStatement;
    }

    /**
     * @param \PDOStatement $pdoStatement
     * @return $this
     */
    public function setPdoStatement(\PDOStatement $pdoStatement)
    {
        $this->pdoStatement = $pdoStatement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @param $bindings
     * @return $this
     */
    public function setBindings($bindings)
    {
        $this->bindings = $bindings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @param $memory
     * @return $this
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param $startTime
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param $endTime
     * @return $this
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartMemory()
    {
        return $this->startMemory;
    }

    /**
     * @param $startMemory
     * @return $this
     */
    public function setStartMemory($startMemory)
    {
        $this->startMemory = $startMemory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndMemory()
    {
        return $this->endMemory;
    }

    /**
     * @param $endMemory
     * @return $this
     */
    public function setEndMemory($endMemory)
    {
        $this->endMemory = $endMemory;
        return $this;
    }
}
