<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午3:27
 * Lb framework base container file
 */

namespace lb\components\containers;

use lb\BaseClass;

class Base extends BaseClass
{
    protected $components = [];
    protected static $instance = false;

    private function __construct()
    {

    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __set($component_name, $component_content)
    {
        if (!property_exists('self', $component_name)) {
            $this->components[$component_name] = $component_content;
        }
    }

    public function __get($component_name)
    {
        if (!property_exists('self', $component_name)) {
            if (array_key_exists($component_name, $this->components)) {
                return $this->components[$component_name];
            }
        }
        return false;
    }

    /**
     * @return bool|Base
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        } else {
            return (static::$instance = new static());
        }
    }
}
