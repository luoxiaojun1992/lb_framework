<?php

namespace lb\components\helpers;

use lb\BaseClass;

class ShareMemory extends BaseClass
{
    private $fd;

    private $mode;

    private $privilege;

    private $key;

    private $size;

    const MODE_A = 'a';
    const MODE_W = 'w';
    const MODE_C = 'c';
    const MODE_N = 'n';

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @param mixed $fd
     * @return $this
     */
    public function setFd($fd)
    {
        $this->fd = $fd;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param mixed $privilege
     * @return $this
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * ShareMemory constructor.
     *
     * @param $key
     * @param $mode
     * @param $privilege
     * @param $size
     */
    public function __construct($key, $mode, $privilege, $size)
    {
        $this->setKey($key)->setMode($mode)->setPrivilege($privilege)->setSize($size);
    }

    /**
     * @return mixed
     */
    public function open()
    {
        return $this->setFd(shmop_open($this->getKey(), $this->getMode(), $this->getPrivilege(), $this->getSize()))->getFd();
    }

    /**
     * @param $data
     * @param $offset
     * @return int
     */
    public function write($data, $offset)
    {
        return shmop_write($this->getFd(), $data, $offset);
    }

    /**
     * @param $offset
     * @param $limit
     * @return string
     */
    public function read($offset, $limit)
    {
        return shmop_read($this->getFd(), $offset, $limit);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return shmop_delete($this->getFd());
    }

    public function close()
    {
        shmop_close($this->getFd());
    }
}
