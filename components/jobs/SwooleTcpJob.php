<?php

namespace lb\components\jobs;

class SwooleTcpJob extends BaseJob
{
    public function hanlder($data, $fromId)
    {
        parent::hanlder($data);

        return 'Swoole Tcp Test Success';
    }
}
