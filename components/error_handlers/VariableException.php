<?php

namespace lb\components\error_handlers;

class VariableException extends \Exception
{
    public function __construct($message = '', $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function customMessage()
    {
        $this->message = 'Variable Exception: ' . $this->getMessage();
    }
}
