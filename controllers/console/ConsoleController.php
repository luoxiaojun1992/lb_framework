<?php

namespace lb\controllers\console;

use lb\controllers\BaseController;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleController extends BaseController
{
    /** @var  InputInterface */
    protected $inputer;

    /** @var  ConsoleOutput */
    protected $outputer;

    /** @var  StyleInterface */
    protected $style;

    protected function beforeAction()
    {
        $this->inputer = new ArgvInput();
        $this->outputer = new ConsoleOutput();
        $this->style = new SymfonyStyle($this->inputer, $this->outputer);

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

    /**
     * @param int $max
     */
    protected function startProgress($max = 0)
    {
        $this->style->progressStart($max);
    }

    /**
     * @param int $step
     */
    protected function updateProgress($step = 1)
    {
        $this->style->progressAdvance($step);
    }

    /**
     *
     */
    protected function endProgress()
    {
        $this->style->progressFinish();
    }
}
