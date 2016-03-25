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
    function testParseUrl()
    {
        $_SERVER['PATH_INFO'] = '/home/home/index';

        list($module, $controller, $method) = Router::parseUrl();

        $this->assertEquals('Home', $module);
        $this->assertEquals('Home', $controller);
        $this->assertEquals('index', $method);

    }
}