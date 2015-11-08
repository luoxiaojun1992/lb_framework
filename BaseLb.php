<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:26
 * Lb framework base bootstrap file
 */

namespace lb;

use lb\components\db\mysql\Connection;
use lb\components\error_handlers\Level;
use lb\components\Request;
use lb\components\Route;
use lb\components\containers\Config;
use lb\components\UrlManager;
use lb\components\Security;

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

    // Singleton App
    public static function app()
    {
        if (self::$app instanceof self) {
            return self::$app;
        } else {
            return (self::$app = new self(true));
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

    // Get App Name
    public function getName()
    {
        if (isset($this->containers['config'])) {
            return $this->containers['config']->get('name');
        }
        return '';
    }

    // Get Db Connection
    public function getDb($db_type)
    {
        switch ($db_type) {
            case 'mysql':
                return Connection::component()->conn;
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

    public function getParam($param_name)
    {
        
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
    public function stop()
    {
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
        // Connect Mysql
        $containers['config'] = $config_container;
        Connection::component($containers);

        // Route
        $this->route_info = Route::getInfo();

        // Auto Load
        spl_autoload_register(['self', 'autoload'], true, false);

        // Set Error Level
        Level::set();

        // Input Filter
        Security::inputFilter();
    }

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            if (!$this->route_info['controller'] || !$this->route_info['action']) {
                $this->route_info['controller'] = 'index';
                $this->route_info['action'] = 'index';
            }
            Route::redirect($this->route_info);
        }
    }
}
