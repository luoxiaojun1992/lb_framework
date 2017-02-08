<?php

namespace lb\components\traits;

trait Singleton
{
    protected static $instance;
    protected static $app;

    protected $is_single = false;

    public function __clone()
    {
        //
    }

    /**
     * @return bool
     */
    protected function isSingle()
    {
        return $this->is_single;
    }

    /**
     * @return object
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        } else {
            return (static::$instance = new static());
        }
    }
}
