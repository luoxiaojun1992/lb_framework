<?php

namespace lb\components\queues;

interface JobInterface
{
    public function __construct($handler, $id, $data);
}
