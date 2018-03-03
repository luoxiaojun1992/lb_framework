<?php

namespace lb\applications\console;

use lb\Lb;

class App extends Lb
{
    protected function exitException($exception)
    {
        Lb::app()->error($exception->getTraceAsString());
        dd(implode(':', [$exception->getCode(), $exception->getMessage()]));
    }

    public function __construct($is_single = false)
    {
        // Start App
        try {
            parent::__construct($is_single);
        } catch (\Throwable $throwable) {
            $this->exitException($throwable);
        }
    }

    public function run()
    {
        if (strtolower(php_sapi_name()) === 'cli') {
            // Start App
            try {
                parent::run();
            } catch (\Throwable $throwable) {
                $this->exitException($throwable);
            }
        } else {
            Lb::app()->stop('Unsupported running mode.');
        }
    }
}
