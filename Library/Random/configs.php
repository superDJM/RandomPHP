<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:32
 */

$config = array(
    //DEBUG配置,开发环境改为false
    'debug' => true,
    //错误文件显示偏移量
    'line_offset' => 3,
    //关键目录设置
    'path' => array(
        'PUBLIC_ROOT' => BASE_ROOT . '/Web/Public',
        'APP_ROOT' => BASE_ROOT . '/Application',
        'DB_ROOT' => BASE_ROOT . '/Db',
        'TEMP_ROOT' => BASE_ROOT . '/Temp',
        'TPL_TEMP_ROOT' => BASE_ROOT . '/Temp/Tpl',
    ),
    //时区设置
    'default_timezone' => 'PRC',
//    钩子设置
    'hook' => array(
//            'APP_START' => 'Random\\Hook\\CountTimeHook',
//            'APP_END' => array(
//                'Random\\Hook\\CountTimeHook',
//                'Random\\Hook\\TimeHook',
//            ),
    ),
    //路由设置
    'router' => array(
        'Home\\' => '/Application/Home'
    ),
    //伪后缀
    'suffix' => 'html',
    //数据库设置
    'database' => array(
        'type' => 'mysqli',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'randomphp',
        'port' => 3306,
    ),
    //session设置
    'session' => array(
        'a' => array(
            'b' => 'c',
        ),
        'suffix' => 'randomphp'
    ),
    //cookie设置
    'cookies' => array(
        'suffix' => 'randomphp'
    ),
    //cache设置
    'cache_type' => 'file', //缓存类型(file, redis, memcached)
    'cache_prefix' => 'RandomPHP',
    'file_cache_dir'  => 'Temp/Cache',
    //redis设置
    'redis_options' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
    ),
    //memcache设置
    'memcached_options' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
    ),
);

return $config;