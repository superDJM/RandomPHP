<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/13
 * Time: 19:24
 */

namespace Random;

class DataCache
{
    private static $instance;

    private function __clone()
    {

    }
    
    /**
     * @todo 获得缓存单例
     */
    public static function getInstance()
    {
        $config = Config::get('cache');
        $type = ucwords($config['type']);
        if (!isset(self::$instance)) {
            if (!in_array($type, array('File', 'Redis', 'Memcached'))) {
                $type = 'File';
            }
            $class = 'Random\\Cache\\' . ucwords($type) . 'Cache';
            //依赖注入
            self::$instance = new $class($config);
        }
        return self::$instance;
    }
}