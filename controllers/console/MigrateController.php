<?php

namespace lb\controllers\console;

use lb\Lb;

class MigrateController extends ConsoleController
{
    public function index()
    {
        $app = require Lb::app()->getRootDir() . '/../vendor/robmorgan/phinx/app/phinx.php';
        $app->run();
    }
}
