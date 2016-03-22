<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:41
 */

namespace Random;

/**
 * Class Config
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 配置类
 * @example
 *      $config = new Config(__DIR__);
 *      var_dump($config['key']);
 */
class Config implements \ArrayAccess
{
    protected $path;
    protected $configs = array();

    function __construct($path)
    {
        $this->path = $path . '/Config';
    }

    function offsetGet($key)
    {
        if (empty($this->configs[$key])) {
            $file_path = $this->path . '/' . ucwords($key) . '.php';
            $config = require $file_path;
            $this->configs[$key] = $config;
        }
        return $this->configs[$key];
    }

    function offsetSet($key, $value)
    {
        throw new \Exception("cannot write config file.");
    }

    function offsetExists($key)
    {
        return isset($this->configs[$key]);
    }

    function offsetUnset($key)
    {
        unset($this->configs[$key]);
    }
}