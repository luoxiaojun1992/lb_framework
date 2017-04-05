<?php

namespace lb;

use lb\components\request\RequestContract;
use lb\components\response\ResponseContract;
use lb\components\Route;

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
     * Set Route Info
     *
     * @param $request RequestContract
     */
    protected function setRouteInfo($request)
    {
        $this->route_info = Route::getWebInfo();
        if (!$this->route_info['controller'] || !$this->route_info['action']) {
            $this->route_info['controller'] = 'index';
            $this->route_info['action'] = 'index';
            $home = Lb::app()->getHome();
            if (isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                $this->route_info['controller'] = $home['controller'];
                $this->route_info['action'] = $home['action'];
            }
        }
    }

    /**
     * Init Login Required
     */
    protected function initLoginRequired()
    {
        $routeInfo = $this->route_info;
        if (
            !in_array($routeInfo['controller'], Route::KERNEL_WEB_CTR) ||
            !in_array($routeInfo['action'], Route::KERNEL_WEB_ACTIONS)
        ) {
            $login_required_filter = Lb::app()->getLoginRequiredFilter();
            if (!isset($login_required_filter['controllers'][$routeInfo['controller']][$routeInfo['action']]) ||
                !$login_required_filter['controllers'][$routeInfo['controller']][$routeInfo['action']]) {
                $login_default_url = Lb::app()->getLoginDefaultUrl();
                if (Lb::app()->isLoginRequired() && $login_default_url) {
                    Lb::app()->loginRequired($login_default_url);
                }
            }
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
