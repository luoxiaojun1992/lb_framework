<?php

namespace lb;

use lb\components\request\RequestContract;
use lb\components\response\ResponseContract;

class SwooleLb extends Lb
{
    protected $request;
    protected $response;

    /**
     * SwooleLb constructor.
     * @param RequestContract $request
     * @param ResponseContract $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        parent::__construct();
    }

    /**
     * Init Application
     */
    public function init()
    {
        if (php_sapi_name() === 'cli') {
            $this->setRouteInfo($this->request);
            $this->initWebApp();
        }
    }

    /**
     * Init Web Application
     */
    protected function initWebApp()
    {
        // Init Session
        $this->initSession($this->response);

        // Login Required
        $this->initLoginRequired();

        // Log
        Lb::app()->log(
            Lb::app()->getHostAddress() . ' visit ' . Lb::app()->getUri() . Lb::app()->getQueryString(),
            Lb::app()->getRouteInfo()
        );

        // Security Handler
        $this->securityHandler();

        // Set Http Cache
        $this->setHttpCache();
    }
}
