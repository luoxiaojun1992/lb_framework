<?php

namespace lb\components\traits;

trait Singleton
{
    protected static $instance;

    public function __clone()
    {
        //
    }
}
