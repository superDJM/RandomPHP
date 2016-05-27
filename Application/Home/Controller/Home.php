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

        return new Response('Hello RandomPHP!!');
    }
}