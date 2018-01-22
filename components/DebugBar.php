<?php

namespace lb\components;

use DebugBar\StandardDebugBar;
use lb\Lb;

class DebugBar
{
    const DEBUG_BAR = 'debugbar';

    public static function getInstance()
    {
        if ($debugBar = Lb::app()->getDIContainer()->get(self::DEBUG_BAR)) {
            return $debugBar;
        }

        $debugBar = new StandardDebugBar();
        Lb::app()->getDIContainer()->set(self::DEBUG_BAR, $debugBar);

        return $debugBar;
    }
}
