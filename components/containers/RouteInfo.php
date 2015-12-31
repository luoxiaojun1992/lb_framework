<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/12/31
 * Time: 下午10:20
 * Lb framework route container file
 */

namespace lb\components\containers;

class RouteInfo extends Base
{
    public function set($item_name, $item_value)
    {
        $this->$item_name = $item_value;
    }

    public function get($item_name)
    {
        return $this->$item_name;
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
