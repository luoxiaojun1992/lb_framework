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
     * Start Progress
     *
     * @param int $max
     */
    protected function startProgress($max = 0)
    {
        $this->style->progressStart($max);
    }

    /**
     * Update Progress
     *
     * @param int $step
     */
    protected function updateProgress($step = 1)
    {
        $this->style->progressAdvance($step);
    }

    /**
     * Stop Progress
     */
    protected function endProgress()
    {
        $this->style->progressFinish();
    }

    /**
     * Get Options
     */
    protected function getOptions($config)
    {
        $options = [];
        $argv = \Console_Getopt::readPHPArgv();
        $opts = \Console_Getopt::getopt(array_slice($argv, 2, count($argv) - 2), $config, null, true);
        if (!empty($opts[0]) && is_array($opts[0])) {
            foreach ($opts[0] as $val) {
                if (!empty($val[0]) && !empty($val[1]) && is_string($val[0]) && is_string($val[1])) {
                    $options[$val[0]] = $val[1];
                }
            }
        }
        return $options;
    }
}
