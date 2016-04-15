<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/15
 * Time: 14:01
 */

namespace Random\Cache;
use Random\Config;

/**
 * 实现Memcached缓存类
 */
class MemcachedCache
{
    private static $_handle;
    private static $_prefix;

    public function __construct()
    {
        if (!extension_loaded('memcached')){
            trigger_error("Memcached拓展没开启，请检查");
        }
        $options = Config::get('memcached_options');
        $host = $options['host'];
        $port = $options['port'];
        self::$_handle = new \memcached();
        if (!self::$_handle->addServer($host, $port)){
            trigger_error("连接Memcached失败，请检查是否开启Memcached服务");
        }
        self::$_prefix = Config::get('cache_prefix');
    }

    /**
     * @param  string $key
     * @param  mixed $data
     * @param  int $lifetime
     * @return boolean
     * @todo 设置缓存
     */
    public function set($key, $data, $lifetime=null)
    {
        $key = self::$_prefix.$key;
        if (is_int($lifetime)) {
            $result = self::$_handle->add($key, $data, $lifetime);
        } else {
            $result = self::$_handle->add($key, $data);
        }
        return $result;
    }

    /**
     * @param  string $key
     * @return mixed 数据
     * @todo 得到缓存数据
     */
    public function get($key)
    {
        $key = self::$_prefix.$key;
        $result = self::$_handle->get($key);
        return $result;
    }

    /**
     * @todo 删除一条缓存
     */
    public function delete($key)
    {
        $key = self::$_prefix.$key;
        return self::$_handle->delete($key);
    }

    /**
     * @todo 清除所有缓存
     */
    public function clear()
    {
        return self::$_handle->flush();
    }
}