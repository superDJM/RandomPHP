<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:32
 */

$config = array(
    'default_timezone' => 'PRC',
    'hook' => array(//        'APP_START' => 'Random\\Hook\\TestHook'
    ),
    'router' => array(
        'Home\\' => '/Application/Home'
    ),
    'suffix' => 'html',
    'database' => array(
        'type' => 'mysqli',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'randomphp',
        'port' => 3306,
    )
);

return $config;