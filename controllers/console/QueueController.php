<?php

namespace lb\controllers\console;

use lb\Lb;

class QueueController extends ConsoleController
{
    /**
     * Listen queue
     */
    public function listen()
    {
        $this->writeln('Listening...');

        while (true) {
            $job = Lb::app()->queuePull();
            if ($job) {
                $handler_class = $job->getHandler();
                (new $handler_class)->handle($job);
            }

            usleep(500000);
        }
    }
}
