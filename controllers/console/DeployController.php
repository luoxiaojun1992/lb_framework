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

        $gitBranch = 'master';
        $isUpdate = false;

        $argv = \Console_Getopt::readPHPArgv();
        $opts = \Console_Getopt::getopt(array_slice($argv, 2, count($argv) - 2), 'g::c::', null, true);
        if (!empty($opts[0]) && is_array($opts[0])) {
            foreach ($opts[0] as $val) {
                if (!empty($val[0]) && !empty($val[1]) && is_string($val[0]) && is_string($val[1])) {
                    switch ($val[0]) {
                        case 'g':
                            $gitBranch = $val[1];
                            break;
                        case 'c':
                            $isUpdate = $val[1] == 'update';
                            break;
                    }
                }
            }
        }

        $this->gitPull($gitBranch);
        $this->composerInstall($isUpdate);

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
