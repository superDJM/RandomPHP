<?php

namespace Test;

use Random\Router;

/**
 * Class RouterTest
 * @package Test
 * @author DJM <op87960@gmail.com>
 * @todo 路由测试类
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     * @author DJM <op87960@gmail.com>
     * @dataProvider urlProvide
     * @param string $url
     * @todo test
     */
    function testParseUrl($url)
    {
        $_SERVER['PATH_INFO'] = $url;

        list($module, $controller, $method) = Router::parseUrl();

        $this->assertEquals('Home', $module);
        $this->assertEquals('Home', $controller);
        $this->assertEquals('index', $method);
//        $this->assertEquals($_GET['']);

    }

    /**
     * @author DJM <op87960@gmail.com>
     * @return array
     * @todo dataProvider
     */
    function urlProvide()
    {

        return array(
            array('/home/home/index'),
            array('home/home/index'),
            array('home/home/index/'),
            array('/home/'),
            array('/home/home/'),
            array('home/home'),
            array('/home/123'),
            array('/123'),
            array('home/home/index/asdjkh'),
            array('home/home/index/asdjkh/dasjh'),
            array('home/home/index/asdjkh/dasjh/s')
        );
    }
}