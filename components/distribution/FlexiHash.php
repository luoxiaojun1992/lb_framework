<?php

namespace lb\components\distribution;

use lb\BaseClass;
use lb\components\traits\Singleton;

class FlexiHash extends BaseClass
{
    use Singleton;

    /**
     * @return FlexiHash
     */
    public static function component()
    {
        if (static::$instance instanceof static) {
            $instance = static::$instance;
            $instance->serverList = [];
            $instance->isSorted = false;
            return $instance;
        } else {
            return (static::$instance = new static());
        }
    }

    //服务器列表
    private $serverList = [];
    //记录是否已经排序
    private $isSorted = false;

    //添加一台服务器
    public function addServer($server)
    {
        $hash = $this->mHash($server);
        if (!isset($this->serverList[$hash])) {
            $this->serverList[$hash] = $server;
        }

        //Adding Virtual Nodes
        $virtualHashs = [];
        if (strlen($server) > 1) {
            $virtualHashs[] = $this->mHash(strrev($server));
        }
        $virtualHashs[] = $this->mHash(md5($server));
        $virtualHashs[] = $this->mHash(sha1($server));
        foreach ($virtualHashs as $virtualHash) {
            if (!isset($this->serverList[$virtualHash])) {
                $this->serverList[$virtualHash] = $server;
            }
        }

        //需要重新排序
        $this->isSorted = false;
        return true;
    }

    //添加一组服务器
    public function addServers($servers = [])
    {
        foreach ($servers as $server) {
            $this->addServer($server);
        }

        return $this;
    }

    //移除一台服务器
    public function removeServer($server)
    {
        $hash = $this->mHash($server);
        if (isset($this->serverList[$hash])) {
            unset($this->serverList[$hash]);
        }
        //需要重新排序
        $this->isSorted = false;
        return true;
    }

    //在当前服务器列表查找合适的服务器
    public function lookup($key = null)
    {
        $hash = $this->mHash($key ? : time());
        //先进行倒序排序操作
        if (!$this->isSorted) {
            krsort($this->serverList, SORT_NUMERIC);
            $this->isSorted = true;
        }
        //圆环上顺时针方向查找当前KEY紧邻的一台服务器
        foreach ($this->serverList as $pos => $server) {
            if ($hash >= $pos) {
                return $server;
            }
        }
        //没有找到则返回顺时针方向最后一台服务器
        foreach ($this->serverList as $server) {
            return $server;
        }
        return false;
    }

    //Hash函数
    private function mHash($key)
    {
        $md5 = substr(md5($key), 0, 8);
        $seed = 31;
        $hash = 0;
        for ($i = 0; $i < 8; $i++) {
            $hash = $hash * $seed + ord($md5{$i});
            $i++;
        }
        return $hash & 0x7FFFFFFF;
    }
}
