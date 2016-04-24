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
 *      //初始化目录
 *      Config::getInstance(__DIR__);
 *      var_dump(Config::get('key'));
 */
class Config
{

    /** @var string 配置文件所在目录 */
    protected $path;

    /** @var array 存放配置的数组 */
    protected $configs = array();

    /** @var bool 标志位:配置文件是否全部加载成功 */
    protected $finish = false;

    static $instance;

    function __construct($path)
    {
        $this->path = $path;
    }


    /**
     * @param $path
     * @return Config
     * @author DJM <op87960@gmail.com>
     * @todo 获取Config静态类,并设置路径
     */
    static function getInstance($path)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($path);
        } else {
            self::$instance->path = $path;
        }

        return self::$instance;
    }

    /**
     * @param $key string 获取的key值
     * @return string 配置
     * @author DJM <op87960@gmail.com>
     * @todo 根据$key获取配置(支持多级数组获取)
     */
    static function get($key = null)
    {
        $arr = explode('.', $key);
        $count = count($arr);

        $result = self::$instance->getConfig($arr[0]);
        for ($i = 1; $i < $count; $i++) {
            if (is_array($result)) {
                $result = isset($result[$arr[$i]]) ? $result[$arr[$i]] : '';
            } else {
                $result = '';
                break;
            }

        }
        return $result;
    }

    /**
     * @param $key string 要获取配置的key值
     * @return string 配置
     * @author DJM <op87960@gmail.com>
     * @todo 获取配置
     */
    private function getConfig($key)
    {
        //判断是否加载完成
        if (!$this->finish) {
            //先加载全局配置
            $this->initConfig(__DIR__);
            if ($this->path != __DIR__) {
                //加载module间配置
                $this->initConfig(dirname($this->path));
                //加载module配置
                $this->initConfig($this->path);
                $this->finish = true;
            }
        }
        //$key为空,返回全部
        if (empty($key)) {
            return $this->configs;
        }
        return isset($this->configs[$key]) ? $this->configs[$key] : '';
    }

    /**
     * @param $path string 配置路径
     * @author DJM <op87960@gmail.com>
     * @todo  加载文件系统中的配置
     */
    private function initConfig($path)
    {
        //现加载全局配置
        $file_path = $path . '/configs.php';
        $config = array();
        if (file_exists($file_path)) {
            $config = require_once $file_path;
        }
        if (is_array($config)) {
            $this->configs = array_merge($this->configs, $config);
        }
    }
}