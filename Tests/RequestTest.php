<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 29/3/2016
 * Time: 上午8:46
 */

namespace Test;


use Random\Factory;
use Random\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    function testGetAndPost()
    {
        $_POST['a'] = 'postA';
        $_POST['b'] = 123;

        $_GET['a'] = 'getA';
        $_GET['b'] = 456;
        /** @var Request $request */
        $request = Factory::getRequest();
        //test get
        $this->assertEquals('getA', $request->get('a', 'get'));
        $this->assertEquals(456, $request->get('b', 'get'));

        //test post
        $this->assertEquals('postA', $request->get('a', 'post'));
        $this->assertEquals(123, $request->get('b', 'post'));

        //test mixed
        $this->assertEquals('postA', $request->get('a'));

        //test null
        $this->assertEmpty($request->get('c'));

        //test constant
        $this->assertTrue(IS_POST);
        $this->assertFalse(IS_GET);

    }


    function testSession()
    {
        $request = Factory::getRequest();
        $request->session('djm', '');
        $this->assertEmpty($request->session('djm'));
        $request->session('djm', true);
        $this->assertTrue($request->session('djm'));
    }

    function testCookie()
    {
        $request = Factory::getRequest();
        $request->cookie('djm', '');
        $this->assertEmpty($request->cookie('djm'));
        $request->cookie('djm', true);
        $this->assertTrue($request->cookie('djm'));
    }
}
