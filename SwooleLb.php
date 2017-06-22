<?php

namespace lb;

use lb\components\error_handlers\HttpException;
use lb\components\helpers\HtmlHelper;
use lb\components\request\RequestContract;
use lb\components\response\SwooleResponse;
use lb\components\Route;
use lb\components\Security;
use FilecacheKit;
use MemcacheKit;
use RedisKit;

class SwooleLb extends Lb
{
    protected $request;
    protected $response;

    /**
     * SwooleLb constructor.
     * @param RequestContract $request
     * @param SwooleResponse $response
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
        $this->route_info = Route::getWebInfo($request);
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
                    Lb::app()->loginRequired($login_default_url, $this->request, $this->response);
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
            $this->request->getHostAddress() . ' visit ' . $this->request->getUri() . $this->request->getQueryString(),
            $this->route_info
        );

        // Security Handler
        $this->securityHandler();

        // Set Http Cache
        $this->setHttpCache($this->response);
    }

    /**
     * Security Handler
     */
    protected function securityHandler()
    {
        $routeInfo = $this->route_info;

        // Input Filter
        Security::inputFilter($this->request->getQueryParams());
        Security::inputFilter($this->request->getBodyParams());

        // IP Filter
        Security::ipFilter($routeInfo['controller'], $routeInfo['action'], $this->request);

        // Csrf Token Validation
        Security::validCsrfToken($routeInfo['controller'], $routeInfo['action'], $this->request, $this->response);

        // CORS
        Security::cors($routeInfo['controller'], $routeInfo['action'], $this->response);

        // X-Frame-Options
        Security::x_frame_options($routeInfo['controller'], $routeInfo['action'], $this->response);

        // X-XSS-Protection
        Security::x_xss_protection($routeInfo['controller'], $routeInfo['action'], $this->response);
    }

    /**
     * Run Application
     *
     * @throws HttpException
     */
    public function run()
    {
        if (!$this->isSingle()) {
            if (php_sapi_name() === 'cli') {
                $this->runWebApp();
            }
        } else {
            throw new HttpException('Single run is forbidden.', 500);
        }
    }

    /**
     * Get http response
     *
     * @param $request
     * @param $response
     * @return string
     */
    protected function getHttpResponse($request = null, $response = null)
    {
        // Response cache content
        $is_cache = false;
        $cache_type = null;
        $page_cache_config = Lb::app()->getPageCacheConfig();
        $routeInfo = $this->route_info;
        if (isset($page_cache_config['controllers'][$routeInfo['controller']][$routeInfo['action']])) {
            $is_cache = true;
            $cache_type = $page_cache_config['controllers'][$routeInfo['controller']][$routeInfo['action']];
            if ($page_cache = $this->getPageCache($cache_type)) {
                return $page_cache;
            }
        }

        // Route
        $hproseConfig = Lb::app()->getHproseConfig();
        $thriftProviderConfig = Lb::app()->getThriftProviderConfig();
        if (!empty($thriftProviderConfig[$routeInfo['controller']][$routeInfo['action']])) {
            Route::thrift($routeInfo);
        } elseif (!empty($hproseConfig[$routeInfo['controller']][$routeInfo['action']])) {
            Route::hprose($routeInfo, $request, $response);
        } else {
            ob_start();
            Route::runWebAction($routeInfo, $request, $response);
            $page_content = ob_get_contents();
            ob_end_clean();
            $page_content = $this->compressPage($page_content);
            $is_cache && $this->setPageCache($cache_type, $page_content);
            return $page_content;
        }

        return '';
    }

    /**
     * Run web application
     */
    protected function runWebApp()
    {
        $this->response->getSwooleResponse()->end($this->getHttpResponse($this->request, $this->response));
    }

    /**
     * Get Page Cache
     *
     * @param $cache_type
     * @return string
     */
    protected function getPageCache($cache_type)
    {
        $route_info = $this->route_info;
        $page_cache_key = implode('_', ['page_cache', $route_info['controller'], $route_info['action']]);
        return Lb::app()->getCache($page_cache_key, $cache_type);
    }

    /**
     * Set Page Cache
     *
     * @param $cache_type
     * @param $page_cache
     * @param int $expire
     */
    protected function setPageCache($cache_type, $page_cache, $expire = 60)
    {
        $route_info = $this->route_info;
        $page_cache_key = implode('_', ['page_cache', $route_info['controller'], $route_info['action']]);
        Lb::app()->setCache($page_cache_key, $page_cache, $cache_type, $expire);
    }

    /**
     * Compress page
     *
     * @param $page_content
     * @return string
     */
    protected function compressPage($page_content)
    {
        $page_compress_config = Lb::app()->getPageCompressConfig();
        $routeInfo = $this->route_info;
        if (isset($page_compress_config['controllers'][$routeInfo['controller']][$routeInfo['action']]) &&
            $page_compress_config['controllers'][$routeInfo['controller']][$routeInfo['action']]) {
            return HtmlHelper::compress($page_content);
        }
        return $page_content;
    }
}
