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
        $dir   = Config::get('cache')['dir'];
        $type  = ucwords(Config::get('cache')['type']);
        if (!isset(self::$instance)) {
            if (!in_array($type, array('File'))) {
                $type = 'File';
            }
            $class = 'Random\\Cache\\' . ucwords($type) . 'Cache';
            self::$instance = new $class($dir);
        }
        return self::$instance;
    }
}