<?php

namespace lb\components\jobs;

use lb\components\helpers\JsonHelper;

class SwooleTcpJob extends BaseJob
{
    public function handler($data)
    {
        parent::handler($data);

        return 'Swoole Tcp Test Success, data:' . JsonHelper::encode($this->getData());
    }
}
