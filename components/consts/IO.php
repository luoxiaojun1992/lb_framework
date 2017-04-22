<?php

namespace lb\components\consts;

interface IO
{
    const WRITE = 'w';
    const READ = 'r';
    const BINARY = 'b';
    const WRITE_BINARY = self::WRITE . self::BINARY;
    const READ_BINARY = self::READ . self::BINARY;
    const WRITE_READ = self::WRITE . self::READ;
    const WRITE_READ_BINARY = self::WRITE_READ . self::BINARY;
}
