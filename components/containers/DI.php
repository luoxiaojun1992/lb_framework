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
    const SERVICE_TYPE_INTERFACE = 'interface';
    const SERVICE_TYPE_CLASS = 'class';
    const SERVICE_TYPE_ABSTRACT = 'abstract';
    const SERVICE_TYPE_STRING = 'string';

    public function set($service_name, $service_impl)
    {
        $this->{$service_name} = $service_impl;
    }

    public function get($service_name)
    {
        $service = $this->{$service_name};
        if ($service) {
            $service_type = $this->get_service_type($service);
            switch($service_type) {
                case static::SERVICE_TYPE_CLASS:
                    $reflectionClass = new \ReflectionClass($service);
                    return $this->create_obj_by_reflection_class($reflectionClass);
                case static::SERVICE_TYPE_INTERFACE:
                case static::SERVICE_TYPE_ABSTRACT:
                case static::SERVICE_TYPE_STRING:
                    return $this->get($service);
                default:
                    return $service;
            }
        } else {
            $obj = $this->cretae_obj($service_name);
            if ($obj) {
                return $obj;
            }
        }

        return $service_name;
    }

    public function create_obj_by_reflection_class(ReflectionClass $reflectionClass)
    {
        $reflectionMethod = $reflectionClass->getConstructor();
        $parameters = $reflectionMethod->getParameters();
        $arguments = [];
        foreach($parameters as $parameter) {
            $dependencyClass = $parameter->getClass();
            if ($dependencyClass) {
                $dependencyClassName = $dependencyClass->getName();
                $arguments[] = $this->get($dependencyClassName);
            } else {
                $parameterName = $parameter->getName();
                $arguments[] = $this->get($parameterName);
            }
        }
        return $reflectionClass->newInstanceArgs($arguments);
    }

    public function cretae_obj($className)
    {
        if (class_exists($className)) {
            $reflectionClass = new \ReflectionClass($className);
            if ($reflectionClass->isInstantiable()) {
                return $this->create_obj_by_reflection_class($reflectionClass);
            }
        }
        return null;
    }

    public function get_service_type($service_impl) {
        if (is_string($service_impl)) {
            if (interface_exists($service_impl)) {
                return static::SERVICE_TYPE_INTERFACE;
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
