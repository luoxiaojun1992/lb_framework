<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:26
 * Lb framework base bootstrap file
 */

namespace lb;

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

class BaseLb
{
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
        if (isset($this->containers['config'])) {
            return $this->containers['config']->get('root_dir');
        }
        return '';
    }

    // Get Client IP Address
    public function getClientAddress()
    {
        return Request::getClientAddress();
    }

    // Get Host
    public function getHost()
    {
        return Request::getHost();
    }

    // Get Request URI
    public function getUri()
    {
        return Request::getUri();
    }

    // Ger Host IP Address
    public function getHostAddress()
    {
        return Request::getHostAddress();
    }

    // Get User Agent
    public function getUserAgent()
    {
        return Request::getUserAgent();
    }

    // Get Query String
    public function getQueryString()
    {
        return Request::getQueryString();
    }

    // Get App Name
    public function getName()
    {
        if (isset($this->containers['config'])) {
            return $this->containers['config']->get('name');
        }
        return '';
    }

    // Get Time Zone
    public function getTimeZone()
    {
        if (isset($this->containers['config'])) {
            return $this->containers['config']->get('timeZone');
        }
        return '';
    }

    // Get Cdn Host
    public function getCdnHost()
    {
        if (isset($this->containers['config'])) {
            return trim($this->containers['config']->get('cdn_host'), '/');
        }
        return '';
    }

    // Is Pretty Url
    public function isPrettyUrl()
    {
        $is_pretty_url = false;
        if (isset($this->containers['config'])) {
            $urlManager = $this->containers['config']->get('urlManager');
            if (isset($urlManager['is_pretty_url'])) {
                $is_pretty_url = $urlManager['is_pretty_url'];
            }
        }
        return $is_pretty_url;
    }

    // Get Js Files
    public function getJsFiles($controller_id, $template_id)
    {
        $js_files = [];
        if (isset($this->containers['config'])) {
            $asset_config = $this->containers['config']->get('assets');
            if ($asset_config) {
                if (isset($asset_config[$controller_id][$template_id]['js'])) {
                    $js_files = $asset_config[$controller_id][$template_id]['js'];
                }
            }
        }
        return $js_files;
    }

    // Get Css Files
    public function getCssFiles($controller_id, $template_id)
    {
        $css_files = [];
        if (isset($this->containers['config'])) {
            $asset_config = $this->containers['config']->get('assets');
            if ($asset_config) {
                if (isset($asset_config[$controller_id][$template_id]['css'])) {
                    $css_files = $asset_config[$controller_id][$template_id]['css'];
                }
            }
        }
        return $css_files;
    }

    // Get Db Connection
    public function getDb($db_type, $node_type)
    {
        switch ($db_type) {
            case 'mysql':
                switch ($node_type) {
                    case 'master':
                        return Connection::component()->write_conn;
                    case 'slave':
                        return Connection::component()->read_conn;
                    default:
                        return Connection::component()->write_conn;
                }
                break;
            default:
                return false;
        }
    }

    // Request Redirect
    public function redirect($path, $replace = true, $http_response_code = null)
    {
        UrlManager::redirect($path, $replace, $http_response_code);
    }

    // Create Absolute Url
    public function createAbsoluteUrl($uri, $query_params = [])
    {
        return UrlManager::createAbsoluteUrl($uri, $query_params);
    }

    // Get Http Request Param Value
    public function getParam($param_name)
    {
        return isset($_REQUEST[$param_name]) ? $_REQUEST[$param_name] : false;
    }

    // Get Csrf Token
    public function getCsrfToken()
    {
        return Security::generateCsrfToken();
    }

    // Get Session Value
    public function getSession($session_key)
    {
        return isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : false;
    }

    // Set Session Value
    public function setSession($session_key, $session_value)
    {
        $_SESSION[$session_key] = $session_value;
    }

    // Delete Session
    public function delSession($session_key)
    {
        if (isset($_SESSION[$session_key])) {
            unset($_SESSION[$session_key]);
        }
    }

    // Delete Multi Sessions
    public function delSessions($session_keys)
    {
        foreach ($session_keys as $session_key) {
            Lb::app()->delSession($session_key);
        }
    }

    // Get Cookie Value
    public function getCookie($cookie_key)
    {
        return isset($_COOKIE[$cookie_key]) ? $_COOKIE[$cookie_key] : false;
    }

    // Set Cookie Value
    public function setCookie($cookie_key, $cookie_value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        setcookie($cookie_key, $cookie_value, $expire, $path, $domain, $secure, $httpOnly);
    }

    // Delete Cookie
    public function delCookie($cookie_key)
    {
        if (isset($_COOKIE[$cookie_key])) {
            unset($_COOKIE[$cookie_key]);
        }
    }

    // Delete Multi Cookies
    public function delCookies($cookie_keys)
    {
        foreach ($cookie_keys as $cookie_key) {
            Lb::app()->delCookie($cookie_key);
        }
    }

    // Get Request Method
    public function getRequestMethod()
    {
        return Request::getRequestMethod();
    }

    // Memcache Get
    public function memcacheGet($key)
    {
        return Memcache::component()->get($key);
    }

    // Memcache Set
    public function memcacheSet($key, $value, $expiration = null)
    {
        Memcache::component()->set($key, $value, $expiration);
    }

    // Memcache Delete
    public function memcacheDelete($key)
    {
        Memcache::component()->delete($key);
    }

    // Redis Get
    public function redisGet($key)
    {
        return Redis::component()->get($key);
    }

    // Redis Set
    public function redisSet($key, $value, $expiration = 0)
    {
        Redis::component()->set($key, $value, $expiration);
    }

    // Redis Delete
    public function redisDelete($key)
    {
        Redis::component()->delete($key);
    }

    // Import PHP File
    public function import($path)
    {
        if (file_exists($path) && strtolower(FileHelper::getExtensionName($path)) == 'php') {
            include_once($path);
        }
    }

    // Get environment variable
    public function getEnv($env_name)
    {
        return Environment::getValue($env_name);
    }

    // Send Swift Mail
    public function swiftSend($from_name, $receivers, $subject, $body, $content_type = 'text/html', $charset = 'UTF-8')
    {
        Swift::component()->send($from_name, $receivers, $subject, $body, $content_type, $charset);
    }

    // File Cache Set
    public function fileCacheSet($key, $value, $cache_time = 86400)
    {
        Filecache::component()->add($key, $value, $cache_time);
    }

    // File Cache Get
    public function fileCacheGet($key)
    {
        return Filecache::component()->get($key);
    }

    // File Cache Delete
    public function fileCacheDelete($key)
    {
        Filecache::component()->delete($key);
    }

    // File Cache Flush
    public function fileCacheFlush()
    {
        Filecache::component()->flush();
    }

    // Log Route Info
    public function log($role = 'system', $level = Logger::NOTICE, $message = '', $context = [])
    {
        Log::component()->record($role, $level, $message, $context);
    }

    // Check If Logged In
    public function loginRequired($redirect_url)
    {
        User::loginRequired($redirect_url);
    }

    // Check If is Guest
    public function isGuest()
    {
        return User::isGuest();
    }

    // Log In
    public function login($username, $user_id, $remember_token = '', $timeout = 0)
    {
        User::login($username, $user_id, $remember_token, $timeout);
    }

    // Log Out
    public function logOut()
    {
        User::logOut();
    }

    // Detect Action Exists
    public function isAction()
    {
        $is_action = false;
        if (Lb::app()->isPrettyUrl()) {
            if (!trim(Lb::app()->getUri(), '/') || stripos(Lb::app()->getUri(), '/action/') !== false) {
                $is_action = true;
            }
        } else {
            if (!trim(Lb::app()->getUri(), '/') || stripos(Lb::app()->getQueryString(), 'action=') !== false) {
                $is_action = true;
            }
        }
        return $is_action;
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
                    if (file_exists($class_file_path)) {
                        include_once($class_file_path);
                    }
                }
            }

            // Auto Load Models
            if (strpos($className, 'app\models\\') === 0) {
                $models_dir = $root_dir . DIRECTORY_SEPARATOR . 'models';
                if (is_dir($models_dir)) {
                    $class_file_path = $models_dir . DIRECTORY_SEPARATOR . str_replace('app\models\\', '', $className) . '.php';
                    if (file_exists($class_file_path)) {
                        include_once($class_file_path);
                    }
                }
            }

            // Auto Load Components
            if (strpos($className, 'app\components\\') === 0) {
                $components_dir = $root_dir . DIRECTORY_SEPARATOR . 'components';
                if (is_dir($components_dir)) {
                    $class_file_path = $components_dir . DIRECTORY_SEPARATOR . str_replace('app\components\\', '', $className) . '.php';
                    if (file_exists($class_file_path)) {
                        include_once($class_file_path);
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
            $this->config = include(CONFIG_FILE);
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

        // Login Required
        $login_default_url = $config_container->get('login_default_url');
        if ($config_container->get('login_required') && $login_default_url) {
            Lb::app()->loginRequired($login_default_url);
        }

        // Connect Mysql
        $containers['config'] = $config_container;
        Connection::component($containers);

        // Connect Memcache
        Memcache::component($containers);

        // Connect Redis
        Redis::component($containers);

        // Init Swift Mailer
        Swift::component($containers);

        // Init File Cache
        Filecache::component($containers);

        // Route
        $this->route_info = Route::getInfo();
        if (!$this->route_info['controller'] || !$this->route_info['action']) {
            if (Lb::app()->isAction()) {
                $this->route_info['controller'] = 'index';
                $this->route_info['action'] = 'index';
            }
        }

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
    }

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            Route::redirect($this->route_info);
        }
    }
}
