<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 15/11/7
 * Time: 下午3:26
 * Lb framework config container file
 */

namespace lb\components\containers;

class Config extends Base
{
    public function set($config_name, $config_content)
    {
        try {
            $this->$config_name = $config_content;
        } catch (\Exception $e) {

        }
    }

    public function get($config_name)
    {
        try {
            return $this->$config_name;
        } catch (\Exception $e) {

        }
    }

    /**
     * @return bool|Base
     */
    public static function component()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return (self::$instance = new self());
        }
    }
}
