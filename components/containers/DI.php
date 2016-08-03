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
    const SERVICE_TYPE_CLASS = 'class';
    const SERVICE_TYPE_ABSTRACT = 'abstract';
    const SERVICE_TYPE_STRING = 'string';

    public function set($service_name, $service_impl)
    {
        $this->{$service_name} = $service_impl;
    }

    public function get($service_name)
    {
        return $this->{$service_name};
    }

    protected function get_service_type($service_impl) {
        if (is_string($service_impl)) {
            if (interface_exists($service_impl)) {
                return 'interface';
            }

            if (class_exists($service_impl)) {
                $reflectionClass = new ReflectionClass($service_impl);

                if ($reflectionClass->isInstantiable()) {
                    return static::SERVICE_TYPE_CLASS;
                }

                if ($reflectionClass->isAbstract()) {
                    return static::SERVICE_TYPE_ABSTRACT;
                }
            }

            return static::SERVICE_TYPE_STRING;
        } else {
            return gettype($service_impl);
        }
    }
}

