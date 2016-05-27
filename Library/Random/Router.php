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
        $uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';   //使用path_info模式
        if (!isset($_SERVER['PATH_INFO'])) {
            //s= 兼容模式
            isset($_SERVER['QUERY_STRING']) && $uri = ltrim(strstr($_SERVER['QUERY_STRING'], 's='), 's=');
//            throw new \Exception("系统不支持path_info");
        }

        //伪静态处理
        if (false !== $pos = strpos($uri, '.')) {
            $suffix = Config::get('suffix');
            if (substr($uri, $pos + 1) != $suffix) {
                throw new \Exception("suffix is not supported");
            } else {
                $uri = substr($uri, 0, $pos);
            }
        }

        //参数分解
        $request = explode('/', trim($uri, '/'), 4);

        //不规范的url,统一定位到Home/Home/index
        if (count($request) < 3) {
            return array('Home', 'Home', 'index');
        } elseif (count($request) > 3) {
            //get Param
            preg_replace_callback('/([^\/]+)\/([^\/]+)/', function ($match) use (&$param) {
                $param[strtolower($match[1])] = strip_tags($match[2]);
            }, array_pop($request));

            //把param加入到$_GET
            $_GET = array_merge($param, $_GET);
        }

        //get Module
        $router[] = ucwords(array_shift($request));

        //get Controller
        $router[] = ucwords(array_shift($request));

        //get Method
        $router[] = array_shift($request);


        return $router;
    }
}