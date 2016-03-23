<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午5:24
 */

namespace Random\Hook;


class TestHook
{
    static function run()
    {
        echo 123;
        return true;
    }
}