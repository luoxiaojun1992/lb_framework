<?php
/**
 * Created by PhpStorm.
 * User: luoxiaojun
 * Date: 16/1/2
 * Time: 下午4:25
 * Lb framework base class file
 */

namespace lb;

class BaseClass
{
    protected static $observers = [];

//    public static function setObserver($event, $observer)
//    {
//        static::$observers[] = [
//            'event' => $event,
//            'observer' => $observer,
//        ];
//    }
//
//    public function __call($name, $arguments)
//    {
//        foreach (static::$observers as $observer) {
//            if (isset($observer['event']['static']) && isset($observer['event']['method']) && isset($observer['observer']['type']) && isset($observer['observer']['class']) && isset($observer['observer']['static']) && isset($observer['observer']['method']) && !$observer['event']['static'] && $name == $observer['event']['method']) {
//                $classNameSpace = 'app\\' .$observer['observer']['type'] . 's\\' . $observer['observer']['class'];
//                if (method_exists($classNameSpace, $observer['observer']['method'])) {
//                    if ($observer['observer']['static']) {
//                        $classNameSpace::{$observer['observer']['method']}();
//                    } else {
//                        (new $classNameSpace())->{$observer['observer']['method']}();
//                    }
//                }
//            }
//        }
//    }
//
//    public static function __callStatic($name, $arguments)
//    {
//        foreach (static::$observers as $observer) {
//            if (isset($observer['event']['static']) && isset($observer['event']['method']) && isset($observer['observer']['type']) && isset($observer['observer']['class']) && isset($observer['observer']['static']) && isset($observer['observer']['method']) && $observer['event']['static'] && $name == $observer['event']['method']) {
//                $classNameSpace = 'app\\' .$observer['observer']['type'] . 's\\' . $observer['observer']['class'];
//                if (method_exists($classNameSpace, $observer['observer']['method'])) {
//                    if ($observer['observer']['static']) {
//                        $classNameSpace::{$observer['observer']['method']}();
//                    } else {
//                        (new $classNameSpace())->{$observer['observer']['method']}();
//                    }
//                }
//            }
//        }
//    }

    public static function className()
    {
        return get_called_class();
    }
}
