<?php

namespace lb\components\queues;

interface JobInterface
{
    public function __construct(Callable $handler, $id, $data);
}
