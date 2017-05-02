<?php

namespace lb\components\jobs;

interface JobInterface
{
    public function hanlder($data);

    public function setData($data);

    public function getData();
}
