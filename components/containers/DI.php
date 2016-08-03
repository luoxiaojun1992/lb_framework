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
    public function set($service_name, $service_impl)
    {
        $this->{$service_name} = $service_impl;
    }

    public function get($service_name)
    {
        return $this->{$service_name};
    }
}
