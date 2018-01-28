<?php

namespace lb\components\consts;

interface Event
{
    const LOG_WRITE_EVENT = 'log_write_event';

    const AOP_EVENT = 'aop_event';

    const REQUEST_EVENT = 'request_event';

    const PDO_EVENT = 'pdo_event';

    const SHUTDOWN_EVENT = 'shutdown_event';
}
