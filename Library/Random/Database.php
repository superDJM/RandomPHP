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
            $db_info['debug'] = Config::get('debug');
            if (strcasecmp($db_info['type'], 'mongodb')) {
                $class = 'Random\\Db\\Pdo';
            } else {
                $class = 'Random\\Db\\Mongodb';
            }

            self::$instance = new $class($db_info);
        }
        return self::$instance;
    }
}