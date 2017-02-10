<?php

namespace lb\components\queues;

interface QueueInterface
{
    public function push(Job $job);

    public function pull();

    public function delay(Job $job, $execute_at);

    public function delete(Job $job);

    public function init();
}
