<?php

namespace lb\components;

use DebugBar\StandardDebugBar;
use lb\BaseClass;
use lb\Lb;

class DebugBar extends BaseClass
{
    const DEBUG_BAR = 'debugbar';

    public static function getInstance()
    {
        $debugBar = Lb::app()->getDIContainer()->get(self::DEBUG_BAR);
        if ($debugBar instanceof StandardDebugBar) {
            return $debugBar;
        }

        $debugBar = new StandardDebugBar();
        Lb::app()->getDIContainer()->set(self::DEBUG_BAR, $debugBar);

        return $debugBar;
    }
}
