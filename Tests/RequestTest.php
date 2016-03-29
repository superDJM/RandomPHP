<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 29/3/2016
 * Time: 上午8:46
 */

namespace Test;


use Random\Factory;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    function testGetAndPost()
    {
        $_POST['a'] = 123;
        $_POST['b'] = '456';

        $_GET['a'] = 123;
        $_GET['b'] = '456';

        $request = Factory::getRequest();
        $this->assertEquals(123, $request->get['a']);
        $this->assertEquals('456', $request->get['b']);

        $this->assertEquals(123, $request->post['a']);
        $this->assertEquals('456', $request->post['b']);
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
