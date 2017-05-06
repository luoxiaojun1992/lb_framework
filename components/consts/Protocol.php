<?php

namespace lb\components\consts;

interface Protocol
{
    const EOF = '\r\n\r\n';

    const SWOOLE_TCP_EOF = '#swoole_tcp_eof#';
}
