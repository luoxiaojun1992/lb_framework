<?php

namespace lb\components\jobs;

use lb\components\helpers\JsonHelper;

class SwooleTcpJob extends BaseJob
{
    public function hanlder($data)
    {
        parent::hanlder($data);

        return 'Swoole Tcp Test Success, data:' . JsonHelper::encode($this->getData());
    }
}
