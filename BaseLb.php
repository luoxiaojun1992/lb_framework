<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:26
 * Lb framework base bootstrap file
 */

namespace lb;

use lb\components\containers\RouteInfo;
use lb\components\error_handlers\HttpException;
use lb\components\helpers\HtmlHelper;
use lb\components\helpers\ImageHelper;
use lb\components\helpers\SystemHelper;
use lb\components\Pagination;
use lb\components\User;
use Monolog\Logger;
use lb\components\cache\Filecache;
use lb\components\cache\Memcache;
use lb\components\cache\Redis;
use lb\components\db\mysql\Connection;
use lb\components\Environment;
use lb\components\error_handlers\Level;
use lb\components\Log;
use lb\components\mailer\Swift;
use lb\components\Request;
use lb\components\Route;
use lb\components\containers\Config;
use lb\components\UrlManager;
use lb\components\Security;
use lb\components\helpers\FileHelper;

class BaseLb extends BaseClass
{
    const VERSION = '1.0.0'; // Framework Version

    protected static $app;

    public $config = []; // App Configuration
    protected $is_single = false;
    protected $route_info = [];
    public $containers = [];

    public function __construct($is_single = false)
    {
        if ($is_single) {
            $this->is_single = $is_single;
        } else {
            // Init
            $this->init();
        }
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    // Singleton App
    public static function app()
    {
        if (static::$app instanceof self) {
            return static::$app;
        } else {
            return (static::$app = new static(true));
        }
    }

    // Get App Root Directory
    public function getRootDir()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('root_dir');
            }
        }
        return '';
    }

    // Get Client IP Address
    public function getClientAddress()
    {
        if ($this->is_single) {
            return Request::getClientAddress();
        }
        return '';
    }

    // Get Host
    public function getHost()
    {
        if ($this->is_single) {
            return Request::getHost();
        }
        return '';
    }

    // Get Request URI
    public function getUri()
    {
        if ($this->is_single) {
            return Request::getUri();
        }
        return '';
    }

    // Ger Host IP Address
    public function getHostAddress()
    {
        if ($this->is_single) {
            return Request::getHostAddress();
        }
        return '';
    }

    // Get User Agent
    public function getUserAgent()
    {
        if ($this->is_single) {
            return Request::getUserAgent();
        }
        return '';
    }

    // Get Query String
    public function getQueryString()
    {
        if ($this->is_single) {
            return Request::getQueryString();
        }
        return '';
    }

    // Get App Name
    public function getName()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('name');
            }
        }
        return '';
    }

    // Get Http Port
    public function getHttpPort()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('http_port');
            }
        }
        return '';
    }

    // Get Time Zone
    public function getTimeZone()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('timeZone');
            }
        }
        return '';
    }

    // Get Cdn Host
    public function getCdnHost()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return trim($this->containers['config']->get('cdn_host'), '/');
            }
        }
        return '';
    }

    // Get Seo Settings
    public function getSeo()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('seo');
            }
        }
        return [];
    }

    // Get Custom Configuration
    public function getCustomConfig()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('custom');
            }
        }
        return [];
    }

    // Get Home Controller & Action
    public function getHome()
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get('home');
            }
        }
        return [];
    }

    // Get DB Config
    public function getDbConfig($db_type)
    {
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                return $this->containers['config']->get($db_type);
            }
        }
        return [];
    }

    // Get Route Info
    public function getRouteInfo()
    {
        if ($this->is_single) {
            if (isset($this->containers['route_info']) && ($controller = $this->containers['route_info']->get('controller')) && ($action = $this->containers['route_info']->get('action'))) {
                return ['controller' => $controller, 'action' => $action];
            }
        }
        return ['controller' => 'index', 'action' => 'index'];
    }

    // If is home
    public function isHome()
    {
        if ($this->is_single) {
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
        if ($this->is_single) {
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
        if ($this->is_single) {
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
        if ($this->is_single) {
            $referer = $this->getReferer();
            if ($referer) {
                $this->redirect($referer);
            }
        }
    }

    // Is Pretty Url
    public function isPrettyUrl()
    {
        $is_pretty_url = false;
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                $urlManager = $this->containers['config']->get('urlManager');
                if (isset($urlManager['is_pretty_url'])) {
                    $is_pretty_url = $urlManager['is_pretty_url'];
                }
            }
        }
        return $is_pretty_url;
    }

    // Get Js Files
    public function getJsFiles($controller_id, $template_id)
    {
        $js_files = [];
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                $asset_config = $this->containers['config']->get('assets');
                if ($asset_config) {
                    if (isset($asset_config[$controller_id][$template_id]['js'])) {
                        $js_files = $asset_config[$controller_id][$template_id]['js'];
                    }
                }
            }
        }
        return $js_files;
    }

    // Get Css Files
    public function getCssFiles($controller_id, $template_id)
    {
        $css_files = [];
        if ($this->is_single) {
            if (isset($this->containers['config'])) {
                $asset_config = $this->containers['config']->get('assets');
                if ($asset_config) {
                    if (isset($asset_config[$controller_id][$template_id]['css'])) {
                        $css_files = $asset_config[$controller_id][$template_id]['css'];
                    }
                }
            }
        }
        return $css_files;
    }

    // Get Db Connection
    public function getDb($db_type, $node_type)
    {
        if ($this->is_single) {
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
        if ($this->is_single) {
            UrlManager::redirect($path, $replace, $http_response_code);
        }
    }

    // Create Absolute Url
    public function createAbsoluteUrl($uri, $query_params = [], $ssl = false, $port = 80)
    {
        if ($this->is_single) {
            return UrlManager::createAbsoluteUrl($uri, $query_params, $ssl, $port);
        }
        return '';
    }

    // Create Relative Url
    public function createRelativeUrl($uri, $query_params = [])
    {
        if ($this->is_single) {
            return UrlManager::createRelativeUrl($uri, $query_params);
        }
        return '';
    }

    // Get Http Request Param Value
    public function getParam($param_name, $default_value = null)
    {
        if ($this->is_single) {
            return isset($_REQUEST[$param_name]) ? $_REQUEST[$param_name] : $default_value;
        }
        return false;
    }

    // Get Csrf Token
    public function getCsrfToken()
    {
        if ($this->is_single) {
            return Security::generateCsrfToken();
        }
        return '';
    }

    // Get Session Value
    public function getSession($session_key)
    {
        if ($this->is_single) {
            return isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : false;
        }
        return false;
    }

    // Set Session Value
    public function setSession($session_key, $session_value)
    {
        if ($this->is_single) {
            $_SESSION[$session_key] = $session_value;
        }
    }

    // Delete Session
    public function delSession($session_key)
    {
        if ($this->is_single) {
            if (isset($_SESSION[$session_key])) {
                unset($_SESSION[$session_key]);
            }
        }
    }

    // Delete Multi Sessions
    public function delSessions($session_keys)
    {
        if ($this->is_single) {
            foreach ($session_keys as $session_key) {
                $this->delSession($session_key);
            }
        }
    }

    // Get Cookie Value
    public function getCookie($cookie_key)
    {
        if ($this->is_single) {
            return isset($_COOKIE[$cookie_key]) ? $_COOKIE[$cookie_key] : false;
        }
        return false;
    }

    // Set Cookie Value
    public function setCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if ($this->is_single) {
            setcookie($cookie_key, $cookie_value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    // Set Cookie Value By Header
    public function setHeaderCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if ($this->is_single) {
            $cookie_str[] = $cookie_key . '=' . $cookie_value;
            if ($expire) {
                $cookie_str[] = 'expires=' . gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT", time() + $expire);
            }
            if ($path) {
                $cookie_str[] = 'path=' . $path;
            }
            if ($domain) {
                $cookie_str[] = 'domain=' . $domain;
            }
            if ($secure) {
                $cookie_str[] = 'secure';
            }
            if ($httpOnly) {
                $cookie_str[] = 'HttpOnly';
            }
            header("Set-Cookie: " . implode('; ', $cookie_str), false);
        }
    }

    // Delete Cookie
    public function delCookie($cookie_key)
    {
        if ($this->is_single) {
            if (isset($_COOKIE[$cookie_key])) {
                setcookie($cookie_key);
            }
        }
    }

    // Delete Multi Cookies
    public function delCookies($cookie_keys)
    {
        if ($this->is_single) {
            foreach ($cookie_keys as $cookie_key) {
                $this->delCookie($cookie_key);
            }
        }
    }

    // Delete Cookie By Header
    public function delHeaderCookie($cookie_key)
    {
        if ($this->is_single) {
            if (isset($_COOKIE[$cookie_key])) {
                $this->setHeaderCookie($cookie_key, $_COOKIE[$cookie_key], -1);
            }
        }
    }

    // Delete Multi Cookies By Header
    public function delHeaderCookies($cookie_keys)
    {
        if ($this->is_single) {
            foreach ($cookie_keys as $cookie_key) {
                $this->delHeaderCookie($cookie_key);
            }
        }
    }

    // Get Request Method
    public function getRequestMethod()
    {
        if ($this->is_single) {
            return Request::getRequestMethod();
        }
        return '';
    }

    // Get Referer
    public function getReferer()
    {
        if ($this->is_single) {
            return Request::getReferer();
        }
        return '';
    }

    // Memcache Get
    public function memcacheGet($key)
    {
        if ($this->is_single) {
            return Memcache::component()->get($key);
        }
        return '';
    }

    // Memcache Set
    public function memcacheSet($key, $value, $expiration = null)
    {
        if ($this->is_single) {
            Memcache::component()->set($key, $value, $expiration);
        }
    }

    // Memcache Delete
    public function memcacheDelete($key)
    {
        if ($this->is_single) {
            Memcache::component()->delete($key);
        }
    }

    // Redis Get
    public function redisGet($key)
    {
        if ($this->is_single) {
            return Redis::component()->get($key);
        }
        return '';
    }

    // Redis Set
    public function redisSet($key, $value, $expiration = 0)
    {
        if ($this->is_single) {
            Redis::component()->set($key, $value, $expiration);
        }
    }

    // Redis Delete
    public function redisDelete($key)
    {
        if ($this->is_single) {
            Redis::component()->delete($key);
        }
    }

    // Import PHP File
    public function import($path)
    {
        if ($this->is_single) {
            if (file_exists($path) && strtolower(FileHelper::getExtensionName($path)) == 'php') {
                include_once($path);
            }
        }
    }

    // Get environment variable
    public function getEnv($env_name)
    {
        if ($this->is_single) {
            return Environment::getValue($env_name);
        }
        return '';
    }

    // Send Swift Mail
    public function swiftSend($from_name, $receivers, $subject, $body, $content_type = 'text/html', $charset = 'UTF-8')
    {
        if ($this->is_single) {
            Swift::component()->send($from_name, $receivers, $subject, $body, $content_type, $charset);
        }
    }

    // File Cache Set
    public function fileCacheSet($key, $value, $cache_time = 86400)
    {
        if ($this->is_single) {
            Filecache::component()->add($key, $value, $cache_time);
        }
    }

    // File Cache Get
    public function fileCacheGet($key)
    {
        if ($this->is_single) {
            return Filecache::component()->get($key);
        }
        return '';
    }

    // File Cache Delete
    public function fileCacheDelete($key)
    {
        if ($this->is_single) {
            Filecache::component()->delete($key);
        }
    }

    // File Cache Flush
    public function fileCacheFlush()
    {
        if ($this->is_single) {
            Filecache::component()->flush();
        }
    }

    // Log Route Info
    public function log($role = 'system', $level = Logger::NOTICE, $message = '', $context = [])
    {
        if ($this->is_single) {
            Log::component()->record($role, $level, $message, $context);
        }
    }

    // Check If Logged In
    public function loginRequired($redirect_url)
    {
        if ($this->is_single) {
            User::loginRequired($redirect_url);
        }
    }

    // Check If is Guest
    public function isGuest()
    {
        if ($this->is_single) {
            return User::isGuest();
        }
        return false;
    }

    // Get User ID
    public function getUserId()
    {
        if ($this->is_single) {
            if (!$this->isGuest()) {
                return $this->getSession('user_id');
            }
        }
        return 0;
    }

    // Get User Name
    public function getUsername()
    {
        if ($this->is_single) {
            if (!$this->isGuest()) {
                return $this->getSession('username');
            }
        }
        return '';
    }

    // Log In
    public function login($username, $user_id, $remember_token = '', $timeout = 0)
    {
        if ($this->is_single) {
            User::login($username, $user_id, $remember_token, $timeout);
        }
    }

    // Log Out
    public function logOut()
    {
        if ($this->is_single) {
            User::logOut();
        }
    }

    // Detect Action Exists
    public function isAction()
    {
        $is_action = false;
        if ($this->is_single) {
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
        if ($this->is_single) {
            ImageHelper::captcha();
        }
    }

    // Get System Version
    public function getVersion()
    {
        if ($this->is_single) {
            return SystemHelper::getVersion();
        }
        return '';
    }

    // Get Pagination
    public function getPagination($total, $page_size, $page = 1)
    {
        if ($this->is_single) {
            return Pagination::getParams($total, $page_size, $page);
        }
        return [];
    }

    // Is Ajax
    public function isAjax()
    {
        if ($this->is_single) {
            return Request::isAjax();
        }
        return false;
    }

    // Autoloader
    protected static function autoload($className)
    {
        $root_dir = Lb::app()->getRootDir();
        if ($root_dir) {
            // Auto Load Controllers
            if (strpos($className, 'app\controllers\\') === 0) {
                $controllers_dir = $root_dir . DIRECTORY_SEPARATOR . 'controllers';
                if (is_dir($controllers_dir)) {
                    $class_file_path = $controllers_dir . DIRECTORY_SEPARATOR . str_replace('app\controllers\\', '', $className) . 'Controller.php';
                    if (file_exists(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path))) {
                        include_once(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                    }
                }
            }

            // Auto Load Models
            if (strpos($className, 'app\models\\') === 0) {
                $models_dir = $root_dir . DIRECTORY_SEPARATOR . 'models';
                if (is_dir($models_dir)) {
                    $class_file_path = $models_dir . DIRECTORY_SEPARATOR . str_replace('app\models\\', '', $className) . '.php';
                    if (file_exists(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path))) {
                        include_once(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                    }
                }
            }

            // Auto Load Components
            if (strpos($className, 'app\components\\') === 0) {
                $components_dir = $root_dir . DIRECTORY_SEPARATOR . 'components';
                if (is_dir($components_dir)) {
                    $class_file_path = $components_dir . DIRECTORY_SEPARATOR . str_replace('app\components\\', '', $className) . '.php';
                    if (file_exists(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path))) {
                        include_once(str_replace('\\', DIRECTORY_SEPARATOR, $class_file_path));
                    }
                }
            }
        }
    }

    // Stop App
    public function stop($content = '')
    {
        if ($content) {
            echo $content;
        }
        die();
    }

    // Init
    public function init()
    {
        // Init Config
        if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
            $this->config = include_once(CONFIG_FILE);
        }

        // Container Register
        // Register Configuration
        $config_container = Config::component();
        foreach ($this->config as $config_name => $config_content) {
            $config_container->set($config_name, $config_content);
        }
        $this->config = [];

        // Inject Config Container
        Lb::app()->containers['config'] = $config_container;

        // Set Timezone
        $config_time_zone = Lb::app()->getTimeZone();
        if ($config_time_zone) {
            if (date_default_timezone_get() != $config_time_zone) {
                date_default_timezone_set($config_time_zone);
            }
        }

        // Start Session
        session_start();

        // Route
        $this->route_info = Route::getInfo();
        if (!$this->route_info['controller'] || !$this->route_info['action']) {
            if (Lb::app()->isAction()) {
                $this->route_info['controller'] = 'index';
                $this->route_info['action'] = 'index';
                $home = Lb::app()->getHome();
                if (isset($home['controller']) && isset($home['action']) && $home['controller'] && $home['action']) {
                    $this->route_info['controller'] = $home['controller'];
                    $this->route_info['action'] = $home['action'];
                }
            }
        }

        // Register Route Info
        $route_info_container = RouteInfo::component();
        foreach ($this->route_info as $item_name => $item_value) {
            $route_info_container->set($item_name, $item_value);
        }

        // Inject Route Info Container
        Lb::app()->containers['route_info'] = $route_info_container;

        // Login Required
        $login_required_filter = $config_container->get('login_required_filter');
        if (!isset($login_required_filter['controllers'][$this->route_info['controller']][$this->route_info['action']]) || !$login_required_filter['controllers'][$this->route_info['controller']][$this->route_info['action']]) {
            $login_default_url = $config_container->get('login_default_url');
            if ($config_container->get('login_required') && $login_default_url) {
                Lb::app()->loginRequired($login_default_url);
            }
        }

        $containers['config'] = $config_container;

        if (Lb::app()->isAction()) {
            // Connect MySql
            $mysql_config = $config_container->get('mysql');
            if (!isset($mysql_config['filter']['controllers'][$this->route_info['controller']][$this->route_info['action']]) || !$mysql_config['filter']['controllers'][$this->route_info['controller']][$this->route_info['action']][strtolower(Lb::app()->getRequestMethod())]) {
                Connection::component($containers);
            }

            // Connect MongoDB
            \lb\components\db\mongodb\Connection::component($containers);

            // Connect Memcache
            Memcache::component($containers);

            // Connect Redis
            Redis::component($containers);
        }

        // Init Swift Mailer
        Swift::component($containers);

        // Init File Cache
        Filecache::component($containers);

        // Log
        Lb::app()->log('system', Logger::NOTICE, Lb::app()->getHostAddress() . ' visit ' . Lb::app()->getUri() . Lb::app()->getQueryString(), $this->route_info);

        // Auto Load
        spl_autoload_register(['self', 'autoload'], true, false);

        // Set Error Level
        Level::set();

        // IP Filter
        Security::ipFilter($this->route_info['controller'], $this->route_info['action']);

        // Input Filter
        Security::inputFilter();

        // Csrf Token Validation
        Security::validCsrfToken($this->route_info['controller'], $this->route_info['action']);

        // CORS
        Security::cors($this->route_info['controller'], $this->route_info['action']);

        // X-Frame-Options
        Security::x_frame_options($this->route_info['controller'], $this->route_info['action']);

        // Set Triggers
        $triggers_config = $config_container->get('triggers');
        if ($triggers_config) {
            foreach ($triggers_config as $item) {
                if (isset($item['observer']) && isset($item['event']['type']) && isset($item['event']['class'])) {
                    $eventClassNamespace = 'app\\' . $item['event']['type'] . 's\\' . $item['event']['class'];
                    if (method_exists($eventClassNamespace, 'setObserver')) {
                        $eventClassNamespace::setObserver($item['event'], $item['observer']);
                    }
                }
            }
        }
    }

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            $is_to_redirect = true;
            if (isset(Lb::app()->containers['config'])) {
                $html_cache_config = Lb::app()->containers['config']->get('html_cache');
                if (isset($html_cache_config['cache_control']) && isset($html_cache_config['offset'])) {
                    HtmlHelper::setCache($html_cache_config['cache_control'], $html_cache_config['offset']);
                }
                $page_cache_config = Lb::app()->containers['config']->get('page_cache');
                if (isset($page_cache_config['controllers'][$this->route_info['controller']][$this->route_info['action']])) {
                    $cache_type = $page_cache_config['controllers'][$this->route_info['controller']][$this->route_info['action']];
                    switch ($cache_type) {
                        case 'file':
                            $page_cache = Lb::app()->fileCacheGet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]));
                            break;
                        case 'memcache':
                            $page_cache = Lb::app()->memcacheGet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]));
                            break;
                        case 'redis':
                            $page_cache = Lb::app()->redisGet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]));
                            break;
                        default:
                            $page_cache = Lb::app()->fileCacheGet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]));
                    }
                    if ($page_cache) {
                        $is_to_redirect = false;
                        echo $page_cache;
                    } else {
                        ob_start();
                        Route::redirect($this->route_info);
                        $page_cache = HtmlHelper::compress(ob_get_contents());
                        ob_end_clean();
                        switch ($cache_type) {
                            case 'file':
                                Lb::app()->fileCacheSet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]), $page_cache, 60);
                                break;
                            case 'memcache':
                                Lb::app()->memcacheSet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]), $page_cache, 60);
                                break;
                            case 'redis':
                                Lb::app()->redisSet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]), $page_cache, 60);
                                break;
                            default:
                                Lb::app()->fileCacheSet(implode('_', ['page_cache', $this->route_info['controller'], $this->route_info['action']]), $page_cache, 60);
                        }
                        $is_to_redirect = false;
                        echo $page_cache;
                    }
                }
            }
            if ($is_to_redirect) {
                ob_start();
                Route::redirect($this->route_info);
                $page_content = ob_get_contents();
                ob_end_clean();
                if (isset(Lb::app()->containers['config'])) {
                    $page_compress_config = Lb::app()->containers['config']->get('page_compress');
                    if (isset($page_compress_config['controllers'][$this->route_info['controller']][$this->route_info['action']]) && $page_compress_config['controllers'][$this->route_info['controller']][$this->route_info['action']]) {
                        $page_content = HtmlHelper::compress($page_content);
                    }
                }
                echo $page_content;
            }
        } else {
            throw new HttpException('Single run is forbidden.', 500);
        }
    }
}
