<?php
/**
 * Created by PhpStorm.
 * User: DJM
 * Date: 2016/3/20
 * Time: 14:59
 */

namespace Random;

use Random\Http\Request;

/**
 * Class Factory
 * @package Djm
 * @author DJM <op87960@gmail.com>
 * @todo 工厂方法
 */
class Factory
{

//    //拿到数据库单例
//   public static function getDatabase(){
//       $database = Database::getInstance();
//       return $database;
//   }
    /**
     * @todo 拿到配置类的对象
     * @param $path
     * @return Config
     */
    public static function getConfig($path)
    {
        $config = Config::getInstance($path);
        return $config;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 获取请求单例
     */
    static function getRequest()
    {
        $request = Request::getInstance();
        return $request;
    }

    /**
     * @return IDatabase
     * @author DJM <op87960@gmail.com>
     * @todo  获取数据库单例
     */
    static function getDatabase()
    {
        $database = Database::getInstance();
        return $database;
    }

    /**
     * @return object
     * @author qiming.c@foxmail.com
     * @todo  获取缓存单例
     */
    public static function getCache()
    {
        $cache = DataCache::getInstance();
        return $cache;
    }

}