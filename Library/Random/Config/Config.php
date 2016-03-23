<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:32
 */

$config = Array(
    'default_timezone' => 'PRC',
    'hook' => Array(
        'APP_START' => 'Random\\Hook\\TestHook'
    ),
    'router' => Array(
        'Home\\' => '/Application/Home'
    )
);

return $config;