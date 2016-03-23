<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午5:08
 */

namespace Home\Controller;

use Random\Controller;
use Random\Factory;
use Random\Response;

class Home extends Controller
{

    /**
     * @param $config \Random\Config
     * @param $request \Random\Request
     * @return Response
     * @author DJM <op87960@gmail.com>
     * @todo 默认目录
     */
    function index($config, $request)
    {
        $database = Factory::getDatabase();
//        return new Response('Hello RandomPHP!!');
        $this->assign('name', 'RandomPHP');
        $this->display();
//        return new Response('123');
    }
}