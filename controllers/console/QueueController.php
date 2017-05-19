<?php

namespace lb\controllers\console;

use lb\components\queues\jobs\Job;
use lb\components\traits\PcntlSignal;
use lb\Lb;

class QueueController extends ConsoleController
{
    use PcntlSignal;

    /**
     * Listen queue
     */
    public function listen()
    {
        declare(ticks=1);
        $this->listenPcntlSignals([SIGINT, SIGTERM], function(){
            dd('Queue Listener Exited.');
        });

        $this->writeln('Queue Listening...');

        while (true) {
            /** @var Job $job */
            $job = Lb::app()->queuePull();
            if ($job) {
                $job->handle();
            }

            usleep(10000);
        }
    }
}
