<?php

namespace lb\components\queues;

interface HandlerInterface
{
    public function __construct();

    public function handle(Job $job);
}
