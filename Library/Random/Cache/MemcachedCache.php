<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/15
 * Time: 14:01
 */

namespace Random\Cache;
use Random\Config;
use Random\IDataCache;

/**
 * 实现Memcached缓存类
 */
class MemcachedCache implements IDataCache
{
    private static $_handle;
    private static $_prefix;
    protected $_config = array(
        'prefix' => 'randomphp',
        'option' => array(
            'host' => '127.0.0.1',
            'port' => '11211',
        )
    );

    public function __construct($config = array())
    {
        if (!extension_loaded('memcached')){
            trigger_error("Memcached拓展没开启，请检查");
        }
        $this->_config = array_merge($this->_config, $config);
        $host = $this->_config['option']['host'];
        $port = $this->_config['option']['port'];

        self::$_handle = new \memcached();
        if (!self::$_handle->addServer($host, $port)){
            trigger_error("连接Memcached失败，请检查是否开启Memcached服务");
        }
        self::$_prefix = $this->_config['prefix'];
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
     * @param $key
     * @todo 删除一条缓存
     * @return boolean
     */
    public function remove($key)
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