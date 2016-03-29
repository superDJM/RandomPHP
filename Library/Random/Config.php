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
    static $instance;

    function __construct($path)
    {
        $this->path = $path;
    }

    static function getInstance($path)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($path);
        } else {
            self::$instance->path = $path;
        }

        return self::$instance;
    }

    private function getConfig($path)
    {
        $file_path = $path . '/Config/Config.php';
        $config = array();
        if (file_exists($file_path)) {
            $config = require $file_path;
        }
        $this->configs[$path] = $config;
    }

    function offsetGet($key)
    {
        //当前目录配置是否加载,否则先加载配置
        if (!isset($this->configs[$this->path])) {
            $this->getConfig($this->path);
        }

        //检查当前目录配置是否存在此key
        if (key_exists($key, $this->configs[$this->path])) {
            return $this->configs[$this->path][$key];
        } elseif ($this->path == __DIR__) {
            //如果目录为库目录,则寻找结束,直接返回
            return $this->configs[__DIR__][$key];
        }

        //module目录配置是否加载,否则先加载配置
        if (!isset($this->configs[APP_ROOT])) {
            $this->getConfig(APP_ROOT);
        }
        //检查module配置是否存在此key
        if (key_exists($key, $this->configs[APP_ROOT])) {
            return $this->configs[$this->path][$key];
        }

        //全局配置是否加载,否则先加载配置
        if (!isset($this->configs[__DIR__])) {
            $this->getConfig(__DIR__, $key);
        }

        return isset($this->configs[__DIR__][$key]) ? $this->configs[__DIR__][$key] : '';
    }

    function offsetSet($key, $value)
    {
        $this->configs[$this->path][$key] = $value;
//        throw new \Exception("cannot write config file.");
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