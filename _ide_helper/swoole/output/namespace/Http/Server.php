<?php
namespace Swoole\Http;

/**
 * @since 1.8.11
 */
class Server extends \swoole_server
{


    /**
     * @param $ha_name[required]
     * @param $cb[required]
     * @return mixed
     */
    public function on($ha_name, $cb){}

    /**
     * @return mixed
     */
    public function start(){}

    /**
     * @param $host[required]
     * @param $port[required]
     * @param $mode[optional]
     * @param $sock_type[optional]
     * @return mixed
     */
    public function __construct($host, $port, $mode=null, $sock_type=null){}

    /**
     * @param $host[required]
     * @param $port[required]
     * @param $sock_type[required]
     * @return mixed
     */
    public function listen($host, $port, $sock_type){}

    /**
     * @param $host[required]
     * @param $port[required]
     * @param $sock_type[required]
     * @return mixed
     */
    public function addlistener($host, $port, $sock_type){}

    /**
     * @param $zset[required]
     * @return mixed
     */
    public function set($zset){}

    /**
     * @param $fd[required]
     * @param $send_data[required]
     * @param $from_id[optional]
     * @return mixed
     */
    public function send($fd, $send_data, $from_id=null){}

    /**
     * @param $ip[required]
     * @param $port[required]
     * @param $send_data[optional]
     * @return mixed
     */
    public function sendto($ip, $port, $send_data=null){}

    /**
     * @param $conn_fd[required]
     * @param $send_data[required]
     * @return mixed
     */
    public function sendwait($conn_fd, $send_data){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public function exist($fd){}

    /**
     * @param $fd[required]
     * @param $is_protected[optional]
     * @return mixed
     */
    public function protect($fd, $is_protected=null){}

    /**
     * @param $conn_fd[required]
     * @param $filename[required]
     * @return mixed
     */
    public function sendfile($conn_fd, $filename){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public function close($fd){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public function confirm($fd){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public function pause($fd){}

    /**
     * @param $fd[required]
     * @return mixed
     */
    public function resume($fd){}

    /**
     * @param $data[required]
     * @param $worker_id[required]
     * @return mixed
     */
    public function task($data, $worker_id){}

    /**
     * @param $data[required]
     * @param $timeout[optional]
     * @param $worker_id[optional]
     * @return mixed
     */
    public function taskwait($data, $timeout=null, $worker_id=null){}

    /**
     * @param $tasks[required]
     * @param $timeout[optional]
     * @return mixed
     */
    public function taskWaitMulti($tasks, $timeout=null){}

    /**
     * @param $data[required]
     * @return mixed
     */
    public function finish($data){}

    /**
     * @return mixed
     */
    public function reload(){}

    /**
     * @return mixed
     */
    public function shutdown(){}

    /**
     * @return mixed
     */
    public function stop(){}

    /**
     * @return mixed
     */
    public function getLastError(){}

    /**
     * @param $from_id[required]
     * @return mixed
     */
    public function heartbeat($from_id){}

    /**
     * @param $fd[required]
     * @param $from_id[required]
     * @return mixed
     */
    public function connection_info($fd, $from_id){}

    /**
     * @param $start_fd[required]
     * @param $find_count[required]
     * @return mixed
     */
    public function connection_list($start_fd, $find_count){}

    /**
     * @param $fd[required]
     * @param $from_id[required]
     * @return mixed
     */
    public function getClientInfo($fd, $from_id){}

    /**
     * @param $start_fd[required]
     * @param $find_count[required]
     * @return mixed
     */
    public function getClientList($start_fd, $find_count){}

    /**
     * @param $ms[required]
     * @param $callback[required]
     * @param $param[optional]
     * @return mixed
     */
    public function after($ms, $callback, $param=null){}

    /**
     * @param $ms[required]
     * @param $callback[required]
     * @return mixed
     */
    public function tick($ms, $callback){}

    /**
     * @param $timer_id[required]
     * @return mixed
     */
    public function clearTimer($timer_id){}

    /**
     * @param $callback[required]
     * @return mixed
     */
    public function defer($callback){}

    /**
     * @return mixed
     */
    public function sendMessage(){}

    /**
     * @return mixed
     */
    public function addProcess(){}

    /**
     * @return mixed
     */
    public function stats(){}

    /**
     * @param $fd[required]
     * @param $uid[required]
     * @return mixed
     */
    public function bind($fd, $uid){}


}
