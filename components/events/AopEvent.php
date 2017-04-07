<?php

namespace lb\components\events;

class AopEvent extends BaseEvent
{
    public $context;

    public function __construct($context)
    {
        $this->setContext($context);
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
