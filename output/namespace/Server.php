<?php
namespace Swoole;

/**
 * @since 1.8.11
 */
class Server
{


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
     * @param $name[required]
     * @param $cb[required]
     * @return mixed
     */
    public function on($name, $cb){}

    /**
     * @param $zset[required]
     * @return mixed
     */
    public function set($zset){}

    /**
     * @return mixed
     */
    public function start(){}

    /**
     
     * 向客户端发送数据
     *
     *  * $data，发送的数据。TCP协议最大不得超过2M，UDP协议不得超过64K
     *  * 发送成功会返回true，如果连接已被关闭或发送失败会返回false
     *
     * TCP服务器
     * -------------------------------------------------------------------------
     *  * send操作具有原子性，多个进程同时调用send向同一个连接发送数据，不会发生数据混杂
     *  * 如果要发送超过2M的数据，可以将数据写入临时文件，然后通过sendfile接口进行发送
     *
     * swoole-1.6以上版本不需要$from_id
     *
     * UDP服务器
     * ------------------------------------------------------------------------
     *  * send操作会直接在worker进程内发送数据包，不会再经过主进程转发
     *  * 使用fd保存客户端IP，from_id保存from_fd和port
     *  * 如果在onReceive后立即向客户端发送数据，可以不传$reactor_id
     *  * 如果向其他UDP客户端发送数据，必须要传入$reactor_id
     *  * 在外网服务中发送超过64K的数据会分成多个传输单元进行发送，如果其中一个单元丢包，会导致整个包被丢弃。所以外网服务，建议发送1.5K以下的数据包
     *
     * @param $fd[required]
     * @param $send_data[required]
     * @param $from_id[optional]
     * @return bool
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
