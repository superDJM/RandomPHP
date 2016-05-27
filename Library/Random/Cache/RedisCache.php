<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/15
 * Time: 12:56
 */

namespace Random\Cache;
use Random\IDataCache;

/**
 * 实现Redis缓存类
 */
class RedisCache implements IDataCache
{
    private static $_handle;
    private static $_prefix;
    protected $_config = array(
        'prefix' => 'randomphp',
        'option' => array(
            'host' => '127.0.0.1',
            'port' => '6379',
        )
    );

    public function __construct($config = array())
    {
        if (!extension_loaded('redis')){
            trigger_error("Redis拓展没开启，请检查");
        }
        $this->_config = array_merge($this->_config, $config);
        self::$_handle = new \Redis();
        $host =  $this->_config['option']['host'];
        $port =  $this->_config['option']['port'];

        if (!self::$_handle->connect($host, $port)){
            trigger_error("连接Redis失败，请检查是否开启Redis服务");
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
        if (is_object($data) || is_array($data)) {
            $data = json_encode($data);
        }
        $key = self::$_prefix.$key;
        if (is_int($lifetime)) {
            $result = self::$_handle->setex($key, $lifetime, $data);
        } else {
            $result = self::$_handle->set($key, $data);
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
        $json_data = json_decode($result, true);
        if ($json_data) {
            $data = $json_data;
        } else {
            $data = $result;
        }
        return $data;
    }

    /**
     * @param $key
     * @todo 删除一条缓存
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
        return self::$_handle->flushAll();
    }
}