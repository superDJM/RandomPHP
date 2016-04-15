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
        'type' => 'mysqli',
        'host'=>'localhost',
        'username'=>'root',
        'password' => 'root',
        'database' => 'randomphp',
        'port'=>'3306'
    ),
    'cache_type' => 'file',
    'file_cache_dir'  => 'Temp/Cache',
);

return $config;