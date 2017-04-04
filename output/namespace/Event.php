<?php
namespace Swoole;

/**
 * @since 1.8.11
 */
class Event
{


    /**
     * @param $fd[required]
     * @param $cb[required]
     * @return mixed
     */
    public static function add($fd, $cb){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public static function del($fd){}

    /**
     * @return mixed
     */
    public static function set(){}

    /**
     * @return mixed
     */
    public static function _exit(){}

    /**
     * @param $fd[required]
     * @param $data[required]
     * @return mixed
     */
    public static function write($fd, $data){}

    /**
     * @return mixed
     */
    public static function wait(){}

    /**
     * @param $callback[required]
     * @return mixed
     */
    public static function defer($callback){}


}
