<?php
/**
 * Created by PhpStorm.
 * User:  qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/23
 * Time: 17:12
 */

namespace Random;


interface IDataCache
{
    public function get($key);

    public function set($key, $val);

    public function remove($key);

    public function clear();
}