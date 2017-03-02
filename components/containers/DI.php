<?php

namespace lb\components\containers;

class DI extends Base
{
    const SERVICE_TYPE_INTERFACE = 'interface';
    const SERVICE_TYPE_CLASS = 'class';
    const SERVICE_TYPE_ABSTRACT = 'abstract';
    const SERVICE_TYPE_STRING = 'string';
    const SERVICE_TYPE_CALLABLE = 'callable';

    /**
     * @param $service_name
     * @param $service_impl
     */
    public function set($service_name, $service_impl)
    {
        $this->{$service_name} = $service_impl;
    }

    /**
     * @param $service_name
     * @return bool|mixed|null|object
     */
    public function get($service_name)
    {
        $service = $this->{$service_name};
        if ($service) {
            switch($this->getServiceType($service)) {
                case static::SERVICE_TYPE_CLASS:
                    return $this->createObj($service);
                case static::SERVICE_TYPE_INTERFACE:
                case static::SERVICE_TYPE_ABSTRACT:
                case static::SERVICE_TYPE_STRING:
                    return $this->get($service);
                case static::SERVICE_TYPE_CALLABLE:
                    return $this->call($service);
                default:
                    return $service;
            }
        } else {
            return $this->createObjOrCall($service_name);
        }
    }

    /**
     * @param $service
     * @return mixed|null|object
     */
    protected function createObjOrCall($service)
    {
        if ($obj = $this->createObj($service)) {
            return $obj;
        }

        if ($callResult = $this->call($service)) {
            return $callResult;
        }

        return $service;
    }

    /**
     * @param $className
     * @return null|object
     */
    public function createObj($className)
    {
        if (!class_exists($className)) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            return null;
        }

        $arguments = [];
        foreach($reflectionClass->getConstructor()->getParameters() as $parameter) {
            if ($dependencyClass = $parameter->getClass()) {
                $arguments[] = $this->get($dependencyClass->getName());
            } else {
                $arguments[] = $this->get($parameter->getName());
            }
        }
        return $reflectionClass->newInstanceArgs($arguments);
    }

    /**
     * @param $callable
     * @return mixed|null
     */
    public function call($callable)
    {
        if (is_callable($callable)) {
            return call_user_func($callable);
        }
        return null;
    }

    /**
     * @param $service_impl
     * @return string
     */
    protected function getServiceType($service_impl)
    {
        if (is_string($service_impl)) {
            if (interface_exists($service_impl)) {
                return static::SERVICE_TYPE_INTERFACE;
            }

            if (class_exists($service_impl)) {
                $reflectionClass = new \ReflectionClass($service_impl);

                if ($reflectionClass->isInstantiable()) {
                    return static::SERVICE_TYPE_CLASS;
                }

                if ($reflectionClass->isAbstract()) {
                    return static::SERVICE_TYPE_ABSTRACT;
                }
            }

            if (is_callable($service_impl)) {
                return static::SERVICE_TYPE_CALLABLE;
            }

            return static::SERVICE_TYPE_STRING;
        } else {
            if (is_callable($service_impl)) {
                return static::SERVICE_TYPE_CALLABLE;
            }

            return gettype($service_impl);
        }
    }
}
