<?php

namespace lb\controllers\console;

use lb\Lb;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\ParserFactory;

class HelpController extends ConsoleController
{
    public function index()
    {
        $this->writeln('Lb Console Help:');

        $files = array_merge(
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/controllers/console'),
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/vendor/lbsoft/lb_framework/controllers/console')
        );

        foreach ($files as $file) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            $stmts = $parser->parse(file_get_contents($file));
            foreach ($stmts[0]->stmts as $stmt) {
                if ($stmt instanceof Class_) {
                    $className = str_replace('controller', '', strtolower($stmt->name));
                    foreach ($stmt->stmts as $stmt2) {
                        if ($stmt2 instanceof ClassMethod && $stmt2->flags == 1) {
                            $this->writeln($className . '/' . $stmt2->name);
                        }
                    }
                    break;
                }
            }
        }

        //todo parse cache
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
