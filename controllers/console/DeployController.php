<?php

namespace lb\controllers\console;

use lb\Lb;

class DeployController extends ConsoleController
{
    public function initProject()
    {
        $this->writeln('Starting Init Project...');

        $rootDir = Lb::app()->getRootDir();
        $this->changeDir($rootDir);

        $this->writeln('Finished.');
    }

    protected function changeDir($dir)
    {
        exec('cd ' . $dir, $output, $return_var);
        $this->writeln($output);
        $this->writeln($return_var);
    }

    protected function gitPull($branch = 'master')
    {
        exec('git pull origin ' . $branch, $output, $return_var);
        $this->writeln($output);
        $this->writeln($return_var);
    }

    protected function composerInstall($isUpdate = false)
    {
        if ($isUpdate) {
            exec('composer update -vvv', $output, $return_var);
        } else {
            exec('composer install -vvv', $output, $return_var);
        }
        $this->writeln($output);
        $this->writeln($return_var);
    }
}
