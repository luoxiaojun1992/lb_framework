<?php

namespace lb\components\consts;

interface ErrorMsg extends ErrorCode
{
    const PAGE_NOT_FOUND = 'Page not found.';

    const VARIABLE_NOT_DEFINED = 'Variable not defined.';

    const CONTROLLER_NOT_FOUND = 'Controller not found.';

    const ACTION_NOT_FOUND = 'Action not found.';

    const INVALID_PARAM = 'Invalid params.';

    const errorMsg = [
        self::ERROR_PAGE_NOT_FOUND => self::PAGE_NOT_FOUND,
        self::ERROR_VARIABLE_NOT_DEFINED => self::VARIABLE_NOT_DEFINED,
        self::ERROR_CONTROLLER_NOT_FOUND => self::CONTROLLER_NOT_FOUND,
        self::ERROR_ACTION_NOT_FOUND => self::ACTION_NOT_FOUND,
        self::ERROR_INVALID_PARAM => self::INVALID_PARAM,
    ];
}
