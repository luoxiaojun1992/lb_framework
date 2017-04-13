<?php

namespace lb\components\queues\jobs;

interface JobInterface
{
    public function __construct($handler, $id, $data);
}
