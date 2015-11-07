<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午3:27
 * Lb framework base container file
 */

namespace lb\components\containers;

class Base
{
    protected $components = [];
    private static $instance = false;

    protected function __set($component_name, $component_content)
    {
        if (!property_exists('self', $component_name)) {
            $this->components[$component_name] = $component_content;
        }
    }

    protected function __get($component_name)
    {
        if (!property_exists('self', $component_name)) {
            if (array_key_exists($component_name, $this->components)) {
                return $this->components[$component_name];
            }
            return false;
        }
    }

    /**
     * @return bool|Base
     */
    protected static function component()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return (self::$instance = new self());
        }
    }
}
