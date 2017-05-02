<?php

namespace lb\components\jobs;

interface JobInterface
{
    public function handler($data);

    public function setData($data);

    public function getData();
}
