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
        'type'=>'mysqli',
        'host'=>'localhost',
        'username'=>'root',
        'password'=>'admin123',
        'database'=>'test',
        'port'=>'3306'
    )
);

return $config;