<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午3:47
 */

namespace Random;


class Router
{
    /**
     * @author DJM <op87960@gmail.com>
     * @todo url解析
     */
    static function parseUrl()
    {
        $param = Array();
        //        $uri = $_SERVER['SCRIPT_NAME'];
        $uri = $_SERVER['PATH_INFO'];   //使用path_info模式
        $request = explode('/', trim($uri, '/'), 4);

        //get Param
        preg_replace_callback('/([^\/]+)\/([^\/]+)/', function ($match) use (&$param) {
            $param[strtolower($match[1])] = strip_tags($match[2]);
        }, array_pop($request));

        //把param加入到$_GET
        $_GET = array_merge($param, $_GET);


        //get Module
        $router[] = ucwords(array_shift($request));

        //get Controller
        $router[] = ucwords(array_shift($request));

        //get Method
        $router[] = array_shift($request);


        return $router;
    }
}