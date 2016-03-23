<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午5:08
 */

namespace Home\Controller;

use Random\Response;

class Home
{

    /**
     * @param $config \Random\Config
     * @param $request \Random\Request
     * @return Response
     * @author DJM <op87960@gmail.com>
     * @todo
     */
    function index($config, $request)
    {
//        var_dump($request);
        new Autoload();
        $config['123'] = 123;;

        return new Response($config['123']);
    }
}