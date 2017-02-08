<?php

namespace lb\controllers\console;

use lb\Lb;

class SystemController extends ConsoleController
{
    public function version()
    {
        dd(Lb::app()->getVersion());
    }
}
