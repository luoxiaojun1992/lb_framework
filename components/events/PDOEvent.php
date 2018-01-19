<?php

namespace lb\components\events;

class PDOEvent extends BaseEvent
{
    public $statement;
    public $duration;
    public $pdoStatement;
    public $bindings;

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
}
