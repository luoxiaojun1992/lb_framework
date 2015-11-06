<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/6
 * Time: 上午11:31
 * Lb framework bootstrap file
 */

namespace lb;

class Lb extends \lb\BaseLb
{
    protected static $app;

    public $config = [];
    protected $is_single = false;
    protected $route_info = [];

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

    // Autoloader
    protected static function autoload($className)
    {
        if (isset(Lb::app()->config['root_dir'])) {
            $root_dir = Lb::app()->config['root_dir'];
            $controllers_dir = $root_dir . DIRECTORY_SEPARATOR . 'controllers';
            $models_dir = $root_dir . DIRECTORY_SEPARATOR . 'models';
            if (is_dir($controllers_dir)) {
                $class_file_path = $controllers_dir . DIRECTORY_SEPARATOR . $className . 'Controller.php';
                if (file_exists($class_file_path)) {
                    include_once($class_file_path);
                }
            }
        }
    }

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            echo Lb::app()->config['name'];
            if ($this->route_info['controller'] && $this->route_info['action']) {

            }
        }
    }
}
