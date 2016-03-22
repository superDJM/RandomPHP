<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:02
 */

define('APP_ROOT', __DIR__);
//定义调试模式
define('DEBUG', true);

$a = 1;
//我是一个入口文件,在这里载入核心类
require APP_ROOT . '/Library/Random/Core.php';

$core = Random\Core::getInstance();

$core->init()->dispatch();
