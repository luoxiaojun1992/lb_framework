<?php

namespace lb\controllers\console;

use lb\components\helpers\AlgoHelper;
use lb\Lb;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\ParserFactory;

class HelpController extends ConsoleController
{
    public function index()
    {
        $this->writeln('Lb Console Help:' . PHP_EOL);

        $files = array_merge(
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/controllers/console'),
            $this->readConsoleControllers(Lb::app()->getRootDir() . '/vendor/lbsoft/lb_framework/controllers/console')
        );

        $tree = [];

        foreach ($files as $file) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            $stmts = $parser->parse(file_get_contents($file));
            foreach ($stmts[0]->stmts as $stmt) {
                if ($stmt instanceof Class_) {
                    $className = str_replace('controller', '', strtolower($stmt->name));
                    $child = [];
                    foreach ($stmt->stmts as $stmt2) {
                        if ($stmt2 instanceof ClassMethod && $stmt2->flags == 1) {
                            $child[] = ['value' => $stmt2->name, 'children' => []];
                        }
                    }
                    $node = ['value' => $className, 'children' => $child];
                    $tree[] = $node;
                    break;
                }
            }
        }

        $this->dumpTree($tree);

        //todo parse cache
    }

    protected function dumpTree($tree)
    {
        AlgoHelper::depthFirst($tree, function ($node) {
            if ($node['children']) {
                $this->writeln($node['value'] . PHP_EOL);
            } else {
                $this->writeln("|-------" . $node['value'] . PHP_EOL);
            }
        });
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
