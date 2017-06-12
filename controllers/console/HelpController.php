<?php

namespace lb\controllers\console;

use lb\components\helpers\AlgoHelper;
use lb\components\helpers\JsonHelper;
use lb\Lb;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\ParserFactory;

class HelpController extends ConsoleController
{
    /**
     * Console Help
     */
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
            $fileContent = file_get_contents($file);
            $cacheKey = md5($fileContent);
            if ($classNodeCache = Lb::app()->getCache($cacheKey)) {
                $tree[] = JsonHelper::decode($classNodeCache);
            } else {
                $classNode = [];
                $stmts = $parser->parse($fileContent);
                foreach ($stmts[0]->stmts as $stmt) {
                    if ($stmt instanceof Class_) {
                        $className = str_replace('controller', '', strtolower($stmt->name));
                        $children = [];
                        foreach ($stmt->stmts as $stmt2) {
                            if ($stmt2 instanceof ClassMethod && $stmt2->flags == 1) {
                                $methodName = $stmt2->name;
                                $attributes = $stmt2->getAttributes();
                                if (isset($attributes['comments'])) {
                                    /** @var Doc $comment */
                                    $comment = $attributes['comments'][0];
                                    $commentText = $comment->getReformattedText();
                                    $commets = explode(PHP_EOL, $commentText);
                                    $methodName .= (' "' . trim(str_replace('*', '', $commets[1])) . '"');
                                }
                                $children[] = ['value' => $methodName, 'children' => []];
                            }
                        }
                        $classNode = ['value' => $className, 'children' => $children];
                        break;
                    }
                }
                if ($classNode) {
                    $tree[] = $classNode;
                    Lb::app()->setCache($cacheKey, JsonHelper::encode($classNode));
                }
            }
        }

        $this->dumpTree($tree);
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
