<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 25/3/2016
 * Time: 下午3:53
 */

require __DIR__ . '/Autoload.php';
$classLoader = new Random\Autoload();
$classLoader->register();

$classLoader->addNamespace('Random\\', __DIR__);
$classLoader->addNamespace('Test\\', __DIR__ . '/../../Tests');

return $classLoader;