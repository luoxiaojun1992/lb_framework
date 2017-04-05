<?php

namespace lb;

class SwooleLb extends Lb
{
    /**
     * Init Application
     */
    public function init()
    {
        if (php_sapi_name() === 'cli') {
            $this->initWebApp();
        }
    }
}
