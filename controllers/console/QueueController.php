<?php

namespace lb\controllers\console;

use lb\components\queues\Job;
use lb\Lb;

class QueueController extends ConsoleController
{
    /**
     * Listen queue
     */
    public function listen()
    {
        declare(ticks=1);
        pcntl_signal(SIGINT, function(){
            dd('Exited.');
        });

        $this->writeln('Listening...');

        while (true) {
            /** @var Job $job */
            $job = Lb::app()->queuePull();
            if ($job) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    Lb::app()->queuePush($job);
                } else if ($pid == 0) {
                    $handler_class = $job->getHandler();
                    (new $handler_class)->handle($job);
                    $job->setProcessed();
                } else {
                    pcntl_wait($status);
                    if (!$job->isProcessed()) {
                        Lb::app()->queuePush($job);
                    } else {
                        $this->writeln('Processed job ' . $job->getId());
                    }
                }
            }

            usleep(500000);
        }
    }
}
