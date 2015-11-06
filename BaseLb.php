<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:26
 * Lb framework base bootstrap file
 */

namespace lb;

use lb\components\Request;
use lb\components\Route;

class BaseLb
{
    protected static $app;

    public $config = []; // App Configuration
    protected $is_single = false;
    protected $route_info = [];
    public $root_dir = ''; // App Root Directory
    public $name = ''; // App Name

    public function __construct($is_single = false)
    {
        // Init Config
        if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
            $this->config = include(CONFIG_FILE);
        }

        if ($is_single) {
            $this->is_single = $is_single;
        } else {
            // Route
            $this->route_info = Route::getInfo();

            // Auto Load
            spl_autoload_register(['self', 'autoload'], true, true);
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
        if ($this->root_dir && is_dir($this->root_dir)) {
            return $this->root_dir;
        } else {
            if (isset(Lb::app()->config['root_dir'])) {
                return ($this->root_dir = Lb::app()->config['root_dir']);
            }
        }
        return '';
    }

    // Get Client IP Address
    public function getClientAddress()
    {
        return Request::getClientAddress();
    }

    // Get Host IP Address
    public function getHostAddress()
    {
        return Request::getHostAddress();
    }

    // Get App Name
    public function getName()
    {
        if ($this->name) {
            return $this->name;
        } else {
            if (isset(Lb::app()->config['name'])) {
                return ($this->name = Lb::app()->config['name']);
            }
        }
        return '';
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

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            if ($this->route_info['controller'] && $this->route_info['action']) {
                Route::redirect($this->route_info);
            }
        }
    }
}
