<?php

namespace lb\controllers\console;

use lb\components\queues\jobs\Job;
use lb\Lb;

class QueueController extends ConsoleController
{
    /**
     * Listen queue
     */
    public function listen()
    {
        declare(ticks=1);
        $signalCallback = function(){
            dd('Exited.');
        };
        pcntl_signal(SIGINT, $signalCallback);
        pcntl_signal(SIGTERM, $signalCallback);

        $this->writeln('Listening...');

        while (true) {
            /** @var Job $job */
            $job = Lb::app()->queuePull();
            if ($job) {
                $job->handle();
            }

            usleep(500000);
        }
    }
}
