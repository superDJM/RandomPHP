<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午5:08
 */

namespace Home\Controller;

use Home\Model\Accounts;
use Random\Controller;
use Random\Http\Response;

class Home extends Controller
{

    /**
     * @param $request \Random\Http\Request
     * @return Response
     * @author DJM <op87960@gmail.com>
     * @todo 默认目录
     */
    function index($request)
    {
//        $database = Factory::getDatabase();
//        var_dump($config['router']);
//        var_dump($request->get);
//        new Autoload();

//        return new Response('Hello RandomPHP!!');
        //test 模版渲染输出
        $this->assign('name', 'RandomPHP');
        $this->assign('act', 'hi');
        return new Response($this->display());
    }
}