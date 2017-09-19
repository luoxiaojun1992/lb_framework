<?php

namespace lb\components\consts;

use lb\components\helpers\HttpHelper;

interface ErrorCode
{
    const ERROR_NONE = 0;

    const ERROR_PAGE_NOT_FOUND = HttpHelper::STATUS_NOT_FOUND;

    const ERROR_VARIABLE_NOT_DEFINED = 1001;

    const ERROR_CONTROLLER_NOT_FOUND = 1002;

    const ERROR_ACTION_NOT_FOUND = 1003;

    const ERROR_INVALID_PARAM = 1004;
}
