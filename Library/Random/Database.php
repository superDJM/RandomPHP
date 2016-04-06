<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午10:01
 */

namespace Random;


class Database
{
    private static $instance;

    private function __construct()
    {
        return false;
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {

            //返回配置中的数据库实例
            $db_info = Config::get('database');
            $class = 'Random\\Db\\' . ucwords($db_info['type']);

            self::$instance = new $class($db_info['host'], $db_info['username'], $db_info['password'], $db_info['database'], $db_info['port']);
        }
        return self::$instance;
    }
}