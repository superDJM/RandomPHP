<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:02
 */
//网站根目录
define('BASE_ROOT', '../');
//资源目录
define('PUBLIC_ROOT', __DIR__ . '/Public');
//类库目录
define('LIB_ROOT', BASE_ROOT . '/Library');
//逻辑目录
define('APP_ROOT', BASE_ROOT . '/Application');
//定义调试模式
define('DEBUG', true);

//我是一个入口文件,在这里载入核心类
require BASE_ROOT . '/Library/Random/Core.php';

$core = Random\Core::getInstance();

$response = $core->init()->dispatch();

$response->send();

