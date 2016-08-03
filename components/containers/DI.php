<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/8/3
 * Time: 下午3:26
 * Lb framework DI container file
 */

namespace lb\components\containers;

class DI extends Base
{
    public function set($config_name, $config_content)
    {
        $this->$config_name = $config_content;
    }

    public function get($config_name)
    {
        return $this->$config_name;
    }
}
