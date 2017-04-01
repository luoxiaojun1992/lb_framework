<?php

namespace lb\controllers\console;

use lb\controllers\BaseController;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleController extends BaseController
{
    /** @var  ConsoleOutput */
    protected $outputer;

    protected function beforeAction()
    {
        $this->outputer = new ConsoleOutput();

        parent::beforeAction();
    }

    /**
     * @param $messages
     */
    protected function writeln($messages)
    {
        $this->outputer->writeln($messages);
    }

    /**
     * @param $messages
     */
    protected function write($messages)
    {
        $this->outputer->write($messages);
    }
}
