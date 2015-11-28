<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/13
 * Time: 11:25
 * Lb framework file cache component file
 */

namespace lb\components\cache;

use lb\Lb;

class Filecache {
    //Path to cache folder
    public $cache_path = '';
    //Length of time to cache a file, default 1 day (in seconds)
    public $cache_time = 86400;
    //Cache file extension
    public $cache_extension = '.cache';

    public $containers = [];
    protected static $instance = false;

    const CACHE_TYPE = 'filecache';

    /**
     * 构造函数
     */
    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $cache_config = $this->containers['config']->get(static::CACHE_TYPE);
            if ($cache_config) {
                $this->cache_path = Lb::app()->getRootDir() . DIRECTORY_SEPARATOR . 'runtime/cache';
                $this->cache_time = isset($cache_config['cache_time']) ? $cache_config['cache_time'] : $this->cache_time;
                $this->cache_extension = isset($cache_config['cache_extension']) ? $cache_config['cache_extension'] : $this->cache_extension;
                if (!is_dir($this->cache_path)) {
                    mkdir($this->cache_path, 0777, true);
                }
            }
        }
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    //增加一对缓存数据
    public function add($key, $value, $cache_time = 86400)
    {
        if ($cache_time != $this->cache_time) {
            $this->cache_time = $cache_time;
        }
        $filename = $this->_get_cache_file($key);
        //写文件, 文件锁避免出错
        $time = time();
        touch($filename, $time, $time + $this->cache_time);
        file_put_contents($filename, serialize(base64_encode($value)), LOCK_EX);
    }

    //删除对应的一个缓存
    public function delete($key)
    {
        $filename = $this->_get_cache_file($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    //获取缓存
    public function get($key)
    {
        $value = '';
        if ($this->_has_cache($key)) {
            $filename = $this->_get_cache_file($key);
            $file_content = file_get_contents($filename);
            if ($file_content) {
                $value = base64_decode(unserialize($file_content));
            }
        }
        return $value;
    }

    //删除所有缓存
    public function flush()
    {
        $fp = opendir($this->cache_path);
        while(($fn = readdir($fp))) {
            if($fn != '.' && $fn != '..') {
                unlink($this->cache_path . DIRECTORY_SEPARATOR . $fn);
            }
        }
    }

    //是否存在缓存
    private function _has_cache($key)
    {
        $filename = $this->_get_cache_file($key);
        if (file_exists($filename) && (fileatime($filename) >= time())) {
            return true;
        } else {
            $this->delete($key);
        }
        return false;
    }

    //验证cache key是否合法，可以自行增加规则
    private function _is_valid_key($key)
    {
        $is_valid = true;
        if (!$key) {
            $is_valid = false;
        }
        return $is_valid;
    }

    //私有方法
    private function _safe_filename($key)
    {
        if ($this->_is_valid_key($key)) {
            return md5($key);
        }
        //key不合法的时候，均使用默认文件'unvalid_cache_key'，不使用抛出异常，简化使用，增强容错性
        return 'unvalid_cache_key';
    }

    //拼接缓存路径
    private function _get_cache_file($key)
    {
        return $this->cache_path . DIRECTORY_SEPARATOR . $this->_safe_filename($key) . $this->cache_extension;
    }

    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers));
        }
    }
}
