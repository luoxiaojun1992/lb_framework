<?php

namespace lb;

use FilecacheKit;
use lb\components\helpers\HttpHelper;
use lb\components\traits\lb\Cookie as CookieTrait;
use lb\components\traits\lb\Crypt as CryptTrait;
use lb\components\traits\lb\FileCache as FileCacheTrait;
use lb\components\traits\lb\Memcache as MemcacheTrait;
use lb\components\traits\lb\Queue as QueueTrait;
use lb\components\traits\lb\Redis as RedisTrait;
use lb\components\traits\lb\Config as ConfigTrait;
use lb\components\traits\lb\Session as SessionTrait;
use MemcacheKit;
use RedisKit;
use lb\components\facades\FilecacheFacade;
use lb\components\facades\MemcacheFacade;
use lb\components\facades\RedisFacade;
use lb\components\containers\RouteInfo;
use lb\components\containers\DI;
use lb\components\error_handlers\HttpException;
use lb\components\helpers\HtmlHelper;
use lb\components\helpers\ImageHelper;
use lb\components\helpers\SystemHelper;
use lb\components\listeners\BaseListener;
use lb\components\observers\BaseObserver;
use lb\components\Pagination;
use lb\components\session\Session;
use lb\components\traits\Singleton;
use lb\components\User;
use lb\components\db\mysql\Connection;
use lb\components\Environment;
use lb\components\error_handlers\Level;
use lb\components\Log;
use lb\components\mailer\Swift;
use lb\components\Request;
use lb\components\Route;
use lb\components\UrlManager;
use lb\components\Security;
use lb\components\helpers\FileHelper;
use lb\components\utils\IdGenerator;
use Monolog\Logger;

class Lb extends BaseClass
{
    use Singleton;
    use FileCacheTrait;
    use RedisTrait;
    use MemcacheTrait;
    use ConfigTrait;
    use SessionTrait;
    use CookieTrait;
    use QueueTrait;
    use CryptTrait;

    public $config = []; // App Configuration
    protected $route_info = [];
    public $containers = [];

    public function __construct($is_single = false)
    {
        !$is_single && $this->init();
    }

    /**
     * Singleton App
     *
     * @return static
     */
    public static function app()
    {
        if (static::$app instanceof self) {
            return static::$app;
        } else {
            $app = new static(true);
            $app->is_single = true;
            return (static::$app = $app);
        }
    }

    /**
     * Get Client IP Address
     *
     * @return bool|string
     */
    public function getClientAddress()
    {
        if ($this->isSingle()) {
            return Request::getClientAddress();
        }
        return '';
    }

    /**
     * Get Host
     *
     * @return string
     */
    public function getHost()
    {
        if ($this->isSingle()) {
            return Request::getHost();
        }
        return '';
    }

    /**
     * Get Request URI
     *
     * @return string
     */
    public function getUri()
    {
        if ($this->isSingle()) {
            return Request::getUri();
        }
        return '';
    }

    /**
     * Ger Host IP Address
     *
     * @return string
     */
    public function getHostAddress()
    {
        if ($this->isSingle()) {
            return Request::getHostAddress();
        }
        return '';
    }

    /**
     * Get User Agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        if ($this->isSingle()) {
            return Request::getUserAgent();
        }
        return '';
    }

    /**
     * Get Query String
     *
     * @return string
     */
    public function getQueryString()
    {
        if ($this->isSingle()) {
            return Request::getQueryString();
        }
        return '';
    }

    /**
     * Get Http Basic Auth User
     *
     * @return string
     */
    public function getBasicAuthUser()
    {
        if ($this->isSingle()) {
            return Request::getBasicAuthUser();
        }
        return '';
    }

    /**
     * Get Http Basic Auth Password
     *
     * @return string
     */
    public function getBasicAuthPassword()
    {
        if ($this->isSingle()) {
            return Request::getBasicAuthPassword();
        }
        return '';
    }

    /**
     * Get Route Info
     * 
     * @return array
     */
    public function getRouteInfo()
    {
        if ($this->isSingle()) {
            if (isset($this->containers['route_info']) && ($controller = $this->containers['route_info']->get('controller')) &&
                ($action = $this->containers['route_info']->get('action'))) {
                return ['controller' => $controller, 'action' => $action];
            }
        }
        return ['controller' => 'index', 'action' => 'index'];
    }

    // If is home
    public function isHome()
    {
        if ($this->isSingle()) {
            if (isset($this->containers['route_info'])) {
                $home_controller = 'index';
                $home_action = 'index';
                $home = $this->getHome();
                if ($home && isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                    $home_controller = $home['controller'];
                    $home_action = $home['action'];
                }
                if ($this->containers['route_info']->get('controller') == $home_controller && $this->containers['route_info']->get('action') == $home_action) {
                    return true;
                }
            }
        }
        return false;
    }

    // Get Home Uri
    public function getHomeUri()
    {
        $homeUri = '';
        if ($this->isSingle()) {
            $controller = 'index';
            $action = 'index';
            $home = $this->getHome();
            if (isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                $controller = $home['controller'];
                $action = $home['action'];
            }
            if ($this->isPrettyUrl()) {
                $homeUri = $this->createRelativeUrl("/{$controller}/action/{$action}");
            } else {
                $homeUri = $this->createRelativeUrl('/index.php', [$controller, 'action' => $action]);
            }
        }
        return $homeUri;
    }

    // Go Home
    public function goHome()
    {
        if ($this->isSingle()) {
            $controller = 'index';
            $action = 'index';
            $home = $this->getHome();
            if (isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                $controller = $home['controller'];
                $action = $home['action'];
            }
            if ($this->isPrettyUrl()) {
                $home_url = $this->createRelativeUrl("/{$controller}/action/{$action}");
            } else {
                $home_url = $this->createRelativeUrl("/index.php?{$controller}&action={$action}");
            }
            $this->redirect($home_url);
        }
    }

    // Go Back
    public function goBack()
    {
        if ($this->isSingle()) {
            $referer = $this->getReferer();
            if ($referer) {
                $this->redirect($referer);
            }
        }
    }

    // Get Db Connection
    public function getDb($db_type, $node_type)
    {
        if ($this->isSingle()) {
            switch ($db_type) {
                case Connection::DB_TYPE:
                    switch ($node_type) {
                        case 'master':
                            return Connection::component()->write_conn;
                        case 'slave':
                            return Connection::component()->read_conn;
                        default:
                            return Connection::component()->write_conn;
                    }
                    break;
                case \lb\components\db\mongodb\Connection::DB_TYPE :
                    return \lb\components\db\mongodb\Connection::component()->_conn;
                    break;
                default:
                    return false;
            }
        }
        return false;
    }

    // Request Redirect
    public function redirect($path, $replace = true, $http_response_code = null)
    {
        if ($this->isSingle()) {
            UrlManager::redirect($path, $replace, $http_response_code);
        }
    }

    // Create Absolute Url
    public function createAbsoluteUrl($uri, $query_params = [], $ssl = false, $port = 80)
    {
        if ($this->isSingle()) {
            return UrlManager::createAbsoluteUrl($uri, $query_params, $ssl, $port);
        }
        return '';
    }

    // Create Relative Url
    public function createRelativeUrl($uri, $query_params = [])
    {
        if ($this->isSingle()) {
            return UrlManager::createRelativeUrl($uri, $query_params);
        }
        return '';
    }

    // Get Http Request Param Value
    public function getParam($param_name, $default_value = null)
    {
        if ($this->isSingle()) {
            return isset($_REQUEST[$param_name]) ? $_REQUEST[$param_name] : $default_value;
        }
        return false;
    }

    // Get Csrf Token
    public function getCsrfToken()
    {
        if ($this->isSingle()) {
            return Security::generateCsrfToken();
        }
        return '';
    }

    // Get Request Method
    public function getRequestMethod()
    {
        if ($this->isSingle()) {
            return Request::getRequestMethod();
        }
        return '';
    }

    // Get Referer
    public function getReferer()
    {
        if ($this->isSingle()) {
            return Request::getReferer();
        }
        return '';
    }

    // Import PHP File
    public function import($path)
    {
        if ($this->isSingle()) {
            if (file_exists($path) && strtolower(FileHelper::getExtensionName($path)) == 'php') {
                $insecure_codes = [
                    '2f',
                    '2e',
                    '%5c',
                    '%252e',
                    '%255c',
                    '%c0',
                    '%af',
                    '%c1',
                    '%9c',
                ];
                include_once(str_replace($insecure_codes, '', $path));
            }
        }
    }

    // Get environment variable
    public function getEnv($env_name)
    {
        if ($this->isSingle()) {
            return Environment::getValue($env_name);
        }
        return '';
    }

    // Send Swift Mail
    public function swiftSend($from_name, $receivers, $subject, $body, $content_type = 'text/html', $charset = 'UTF-8')
    {
        if ($this->isSingle()) {
            Swift::component()->send($from_name, $receivers, $subject, $body, $content_type, $charset);
        }
    }

    // Log Route Info
    public function log($message = '', $context = [], $level = Logger::NOTICE, $role = 'system')
    {
        if ($this->isSingle()) {
            Log::component()->record($message, $context, $level, $role);
        }
    }

    // Check If Logged In
    public function loginRequired($redirect_url)
    {
        if ($this->isSingle()) {
            User::loginRequired($redirect_url);
        }
    }

    // Check If is Guest
    public function isGuest()
    {
        if ($this->isSingle()) {
            return User::isGuest();
        }
        return false;
    }

    // Get User ID
    public function getUserId()
    {
        if ($this->isSingle()) {
            if (!$this->isGuest()) {
                return $this->getSession('user_id');
            }
        }
        return 0;
    }

    // Get User Name
    public function getUsername()
    {
        if ($this->isSingle()) {
            if (!$this->isGuest()) {
                return $this->getSession('username');
            }
        }
        return '';
    }

    // Log In
    public function login($username, $user_id, $remember_token = '', $timeout = 0)
    {
        if ($this->isSingle()) {
            User::login($username, $user_id, $remember_token, $timeout);
        }
    }

    // Log Out
    public function logOut()
    {
        if ($this->isSingle()) {
            User::logOut();
        }
    }

    // Detect Action Exists
    public function isAction()
    {
        $is_action = false;
        if ($this->isSingle()) {
            if (Lb::app()->isPrettyUrl()) {
                if (!trim(Lb::app()->getUri(), '/') || stripos(Lb::app()->getUri(), '/action/') !== false) {
                    $is_action = true;
                }
            } else {
                if (!trim(Lb::app()->getUri(), '/') || stripos(Lb::app()->getQueryString(), 'action=') !== false) {
                    $is_action = true;
                }
            }
        }
        return $is_action;
    }

    // Output Captcha
    public function captcha()
    {
        if ($this->isSingle()) {
            ImageHelper::captcha();
        }
    }

    // Get System Version
    public function getVersion()
    {
        if ($this->isSingle()) {
            return SystemHelper::getVersion();
        }
        return '';
    }

    // Get Pagination
    public function getPagination($total, $page_size, $page = 1)
    {
        if ($this->isSingle()) {
            return Pagination::getParams($total, $page_size, $page);
        }
        return [];
    }

    // Is Ajax
    public function isAjax()
    {
        if ($this->isSingle()) {
            return Request::isAjax();
        }
        return false;
    }

    // Get RPC Client
    public function get_rpc_client($url)
    {
        if ($this->isSingle()) {
            include_once(Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'hprose' .
                DIRECTORY_SEPARATOR . 'hprose' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Hprose.php');
            return new \Hprose\Http\Client($url);
        }
        return false;
    }

    // Get DI Container
    public function getDIContainer()
    {
        if ($this->isSingle()) {
            return DI::component();
        }
        return false;
    }

    // Register Event Listener
    public function on($event_name, BaseListener $listener, $data = null)
    {
        if ($this->isSingle()) {
            BaseObserver::on($event_name, $listener, $data);
        }
    }

    // Trigger Event
    public function trigger($event_name, $event = null)
    {
        if ($this->isSingle()) {
            BaseObserver::trigger($event_name, $event);
        }
    }

    /**
     * Rewrite uniqid
     *
     * @param string $prefix
     * @return int
     */
    public function uniqid($prefix = '')
    {
        return IdGenerator::component()->generate($prefix);
    }

    // Autoloader
    protected static function autoload($className)
    {
        $root_dir = Lb::app()->getRootDir();
        if ($root_dir) {
            $className = str_replace('..', '', $className);

            // Auto Load Controllers
            if (strpos($className, 'app\controllers\\') === 0) {
                $controllers_dir = $root_dir . DIRECTORY_SEPARATOR . 'controllers';
                if (is_dir($controllers_dir)) {
                    $class_file_path = $controllers_dir . DIRECTORY_SEPARATOR . str_replace('app\controllers\\', '', $className) . 'Controller.php';
                    Lb::app()->import(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                }
            }

            // Auto Load Models
            if (strpos($className, 'app\models\\') === 0) {
                $models_dir = $root_dir . DIRECTORY_SEPARATOR . 'models';
                if (is_dir($models_dir)) {
                    $class_file_path = $models_dir . DIRECTORY_SEPARATOR . str_replace('app\models\\', '', $className) . '.php';
                    Lb::app()->import(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                }
            }

            // Auto Load Components
            if (strpos($className, 'app\components\\') === 0) {
                $components_dir = $root_dir . DIRECTORY_SEPARATOR . 'components';
                if (is_dir($components_dir)) {
                    $class_file_path = $components_dir . DIRECTORY_SEPARATOR . str_replace('app\components\\', '', $className) . '.php';
                    Lb::app()->import(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                }
            }
        }
    }

    // Stop App
    public function stop($content = '', $exit_code = 1)
    {
        $content && @_echo($content);
        die($exit_code);
    }

    /**
     * Load Environment Variables
     */
    protected function loadEnv()
    {
        if (defined('ENV_DIR') && file_exists(ENV_DIR)) {
            if (defined('ENV_FILE') && file_exists(ENV_FILE)) {
                $dotenv = new \Dotenv\Dotenv(ENV_DIR, ENV_FILE);
            } else {
                $dotenv = new \Dotenv\Dotenv(ENV_DIR);
            }
            $dotenv->load();
        }
    }

    /**
     * Set Default Timezone
     */
    protected function setDefaultTimeZone()
    {
        if ($config_time_zone = Lb::app()->getTimeZone()) {
            if (date_default_timezone_get() != $config_time_zone) {
                date_default_timezone_set($config_time_zone);
            }
        }
    }

    /**
     * Security Handler
     */
    protected function securityHandler()
    {
        // Input Filter
        Security::inputFilter();

        // IP Filter
        Security::ipFilter($this->route_info['controller'], $this->route_info['action']);

        // Csrf Token Validation
        Security::validCsrfToken($this->route_info['controller'], $this->route_info['action']);

        // CORS
        Security::cors($this->route_info['controller'], $this->route_info['action']);

        // X-Frame-Options
        Security::x_frame_options($this->route_info['controller'], $this->route_info['action']);

        // X-XSS-Protection
        Security::x_xss_protection($this->route_info['controller'], $this->route_info['action']);
    }

    /**
     * Init Login Required
     */
    protected function initLoginRequired()
    {
        if (!in_array($this->route_info['controller'], Route::KERNEL_WEB_CTR) || !in_array($this->route_info['action'], Route::KERNEL_WEB_ACTIONS)) {
            $config_container = Lb::app()->containers['config'];
            $login_required_filter = $config_container->get('login_required_filter');
            if (!isset($login_required_filter['controllers'][$this->route_info['controller']][$this->route_info['action']]) ||
                !$login_required_filter['controllers'][$this->route_info['controller']][$this->route_info['action']]) {
                $login_default_url = $config_container->get('login_default_url');
                if ($config_container->get('login_required') && $login_default_url) {
                    Lb::app()->loginRequired($login_default_url);
                }
            }
        }
    }

    /**
     * Set Route Info
     */
    protected function setRouteInfo()
    {
        $this->route_info = php_sapi_name() === 'cli' ? Route::getConsoleInfo() : Route::getWebInfo();
        if (!$this->route_info['controller'] || !$this->route_info['action']) {
            $this->route_info['controller'] = 'index';
            $this->route_info['action'] = 'index';
            $home = Lb::app()->getHome();
            if (isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                $this->route_info['controller'] = $home['controller'];
                $this->route_info['action'] = $home['action'];
            }
        }
        $route_info_container = RouteInfo::component();
        foreach ($this->route_info as $item_name => $item_value) {
            $route_info_container->set($item_name, $item_value);
        }
        Lb::app()->containers['route_info'] = $route_info_container;
    }

    /**
     * Register Facades
     */
    protected function registerFacades()
    {
        $facades = [
            'RedisKit' => RedisFacade::class,
            'MemcacheKit' => MemcacheFacade::class,
            'FilecacheKit' => FilecacheFacade::class,
        ];

        array_walk($facades, function ($facade, $alias) {
            class_alias($facade, $alias);
        });
    }

    /**
     * Init Session
     */
    protected function initSession()
    {
        if ($session_config = Lb::app()->containers['config']->get('session')) {
            if (isset($session_config['type'])) {
                Session::set_session($session_config['type']);
            }
        }
        session_start();
    }

    /**
     * Init Application
     *
     * @throws HttpException
     */
    public function init()
    {
        $this->initCommon();

        if (php_sapi_name() !== 'cli') {
            if (!Lb::app()->isAction()) {
                throw new HttpException(self::PAGE_NOT_FOUND, 404);
            }

            $this->initWebApp();
        } else {
            $this->initConsoleApp();
        }
    }

    /**
     * Common init
     */
    protected function initCommon()
    {
        // Load Environment Variables
        $this->loadEnv();

        // Include Helper Functions
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'Functions.php');

        // Init Config
        $this->initConfig();

        // Set mb internal encoding
        mb_internal_encoding(Lb::app()->getMbInternalEncoding() ?: 'UTF-8');

        // Set Timezone
        $this->setDefaultTimeZone();

        // Register Facades
        $this->registerFacades();

        // Autoload
        spl_autoload_register(['self', 'autoload'], true, false);

        // Set Error Level
        Level::set();

        // Route
        $this->setRouteInfo();
    }

    /**
     * Init Web Application
     */
    protected function initWebApp()
    {
        // Init Session
        $this->initSession();

        // Login Required
        $this->initLoginRequired();

        // Log
        Lb::app()->log(
            Lb::app()->getHostAddress() . ' visit ' . Lb::app()->getUri() . Lb::app()->getQueryString(),
            $this->route_info
        );

        // Security Handler
        $this->securityHandler();

        // Set Http Cache
        $this->setHttpCache();
    }

    /**
     * Run Console Application
     */
    protected function initConsoleApp()
    {
        //
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
        switch ($cache_type) {
            case FilecacheKit::CACHE_TYPE:
                return Lb::app()->fileCacheGet($page_cache_key);
            case MemcacheKit::CACHE_TYPE:
                return Lb::app()->memcacheGet($page_cache_key);
            case RedisKit::CACHE_TYPE:
                return Lb::app()->redisGet($page_cache_key);
            default:
                return Lb::app()->fileCacheGet($page_cache_key);
        }
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
        switch ($cache_type) {
            case FilecacheKit::CACHE_TYPE:
                Lb::app()->fileCacheSet($page_cache_key, $page_cache, $expire);
                break;
            case MemcacheKit::CACHE_TYPE:
                Lb::app()->memcacheSet($page_cache_key, $page_cache, $expire);
                break;
            case RedisKit::CACHE_TYPE:
                Lb::app()->redisSet($page_cache_key, $page_cache, $expire);
                break;
            default:
                Lb::app()->fileCacheSet($page_cache_key, $page_cache, $expire);
        }
    }

    /**
     * Set Http Cache
     */
    protected function setHttpCache()
    {
        $html_cache_config = Lb::app()->containers['config']->get('html_cache');
        if (isset($html_cache_config['cache_control']) && isset($html_cache_config['offset'])) {
            HttpHelper::setCache($html_cache_config['cache_control'], $html_cache_config['offset']);
        }
    }

    /**
     * Compress page
     *
     * @param $page_content
     * @return string
     */
    protected function compressPage($page_content)
    {
        $page_compress_config = Lb::app()->containers['config']->get('page_compress');
        if (isset($page_compress_config['controllers'][$this->route_info['controller']][$this->route_info['action']]) &&
            $page_compress_config['controllers'][$this->route_info['controller']][$this->route_info['action']]) {
            return HtmlHelper::compress($page_content);
        }
        return $page_content;
    }

    /**
     * Run Application
     *
     * @throws HttpException
     */
    public function run()
    {
        if (!$this->isSingle()) {
            if (php_sapi_name() !== 'cli') {
                $this->runWebApp();
            } else {
                $this->runConsoleApp();
            }
        } else {
            throw new HttpException('Single run is forbidden.', 500);
        }
    }

    /**
     * Run web application
     */
    protected function runWebApp()
    {
        // Response cache content
        $is_cache = false;
        $cache_type = null;
        $page_cache_config = Lb::app()->containers['config']->get('page_cache');
        if (isset($page_cache_config['controllers'][$this->route_info['controller']][$this->route_info['action']])) {
            $is_cache = true;
            $cache_type = $page_cache_config['controllers'][$this->route_info['controller']][$this->route_info['action']];
            if ($page_cache = $this->getPageCache($cache_type)) {
                @_echo($page_cache);
                return;
            }
        }

        // Route
        $rpc_config = Lb::app()->getRpcConfig();
        if (isset($rpc_config[$this->route_info['controller']][$this->route_info['action']]) &&
            $rpc_config[$this->route_info['controller']][$this->route_info['action']]) {
            Route::rpc($this->route_info);
        } else {
            ob_start();
            Route::runWebAction($this->route_info);
            $page_content = ob_get_contents();
            ob_end_clean();
            $page_content = $this->compressPage($page_content);
            $is_cache && $this->setPageCache($cache_type, $page_content);
            @_echo($page_content);
        }
    }

    /**
     * Run console application
     */
    protected function runConsoleApp()
    {
        Route::runConsoleAction($this->route_info);
    }
}
