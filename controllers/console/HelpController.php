<?php

namespace lb\controllers\console;

use lb\Lb;

class HelpController extends ConsoleController
{
    public function index()
    {
        $files = array_merge(
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/controllers/console'),
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/vendor/lbsoft/lb_framework/controllers/console')
        );
        var_dump($files);
        $this->writeln('Building...');
    }

    protected function readConsoleControllers($dir)
    {
        $files = [];
        $fd = opendir($dir);
        while ($file = readdir($fd)) {
            if (!in_array($file, ['.', '..'])) {
                if (!is_dir($file)) {
                    $files[] = $dir . '/' . $file;
                } else {
                    $files = array_merge($files, $this->readConsoleControllers($file));
                }
            }
        }
        closedir($fd);
        return $files;
    }
}
