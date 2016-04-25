<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 4/4/2016
 * Time: 下午2:28
 */

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    header("Content-Type: text/html; charset=UTF-8");
    echo 'PHP环境不能低于5.3.0';
    exit;
}

//我是一个入口文件,在这里载入自动载入类
$classLoader = require 'InitAutoLoad.php';

//初始化config
Random\Factory::getConfig(__DIR__);

$config = Random\Config::get();

//得到核心类
$core = Random\Core::getInstance($config, $classLoader);

//执行
$response = $core->init()->dispatch();

//输出到浏览器
$response->send();