<?php

namespace lb\controllers\console;

use lb\Lb;

class SeedController extends ConsoleController
{
    protected function runMigrate()
    {
        $app = require Lb::app()->getRootDir() . '/vendor/robmorgan/phinx/app/phinx.php';
        $app->run();
    }

    protected function setArgv($method = '')
    {
        $argv = $_SERVER['argv'];
        $_SERVER['argv'] = $method ? ['Lb Migrate', $method] : ['Lb Migrate'];
        if ($_SERVER['argc'] > 2) {
            foreach ($argv as $k => $v) {
                if ($k > 1) {
                    $_SERVER['argv'][] = $v;
                }
            }
        }
    }

    public function create()
    {
        $this->setArgv('seed:create');
        $this->runMigrate();
    }

    public function run()
    {
        $this->setArgv('seed:run');
        $this->runMigrate();
    }
}
