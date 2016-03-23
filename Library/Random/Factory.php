<?php
/**
 * Created by PhpStorm.
 * User: DJM
 * Date: 2016/3/20
 * Time: 14:59
 */

namespace Random;


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

    static function getRequest()
    {
        if (empty(Register::get('request'))) {
            $request = Request::getInstance();
            Register::set('request', $request);
        }
        return Register::get('request');
    }

}