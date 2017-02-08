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
        declare(ticks=1);

        $this->writeln('Listening...');

        while (true) {
            $job = Lb::app()->queuePull();
            if ($job) {
                $handler_class = $job->getHandler();
                try {
                    (new $handler_class)->handle($job);
                } catch (\Exception $e) {
                    Lb::app()->queuePush($job);
                }
            }

            usleep(500000);
        }

        dd('Exited.');
    }
}
