<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午10:39
 */

$config = Array(
    'router' => Array(
        'Home\\' => 'djm'
    ),
    'database'=>Array(
        'type' => 'mysql',
        'host'=>'localhost',
        'username'=>'root',
        'password' => '',
        'database' => 'randomphp',
        'port' => '3306',
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 1,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => true,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        'slave' => array(
            0 => array(
                'type' => 'mysql',
                'host' => '42.96.186.216',
                'username' => 'root',
                'password' => '',
                'database' => 'randomphp',
                'port' => 3306,
                'weight' => '1',
            )
        )
    )
);

return $config;