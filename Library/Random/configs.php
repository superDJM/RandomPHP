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
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'randomphp',
        'port' => 3306,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        'slave' => array(
            0 => array(
                'type' => 'mysql',
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'database' => 'randomphp',
                'port' => 3306,
                'weight' => '1',
            )
        )
    ),
    'template' => array(
        'com_suffix' => 'tpl',
        'TPL_TEMP_ROOT' => BASE_ROOT . '/Temp/Tpl',
        'cache' => true,
        'cache_suffix' => 'htm',
        'expire' => 60,
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
    'cache' => array(
        'type' => 'file',   //缓存类型(file, redis, memcached)
        'prefix' => 'randomphp',
        'dir' => BASE_ROOT . '/Temp/Cache',
        'option' => array(
            'host' => '127.0.0.1',
            'port' => '11211',
        )
    ),
);

return $config;