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

    public $config;
    protected $is_single = false;
    protected $route_info = [];

    public function __construct($is_single = false)
    {
        if ($is_single) {
            $this->is_single = $is_single;
        }

        // Init Config
        if (defined('CONFIG_FILE') && file_exists(CONFIG_FILE)) {
            $this->config = include(CONFIG_FILE);
        }

        // Route
        Route::getControllerAction();
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

    // Start App
    public function run()
    {
        if (!$this->is_single) {
            echo Lb::app()->config['name'];
        }
    }
}
