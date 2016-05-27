<?php

namespace Test;

use Random\Config;

/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 4/4/2016
 * Time: 下午4:50
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @author DJM <op87960@gmail.com>
     * @param $path string
     * @todo 初始化配置
     */
    public function initConfig($path)
    {
        if(!defined('BASE_ROOT')){
            define('BASE_ROOT', '../');
        }
        Config::getInstance($path);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 测试get一维或多维数组
     */
    public function testGetOneOrMoreConfig()
    {
        $this->initConfig('..//Library/Random');
        $this->assertEquals('PRC', Config::get('default_timezone'));
        $this->assertEquals('mysql', Config::get('database.type'));
        $this->assertEquals('c', Config::get('session.a.b'));
        $this->assertEmpty(Config::get('session.a.b.'));
        $this->assertEmpty(Config::get('a'));
        $this->assertEmpty(Config::get('a.b'));
        $this->assertEmpty(Config::get('.a.b'));
        $this->assertArrayHasKey('suffix', Config::get());
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 测试多级目录覆盖get的值
     */
    public function testGetCoverConfig()
    {
        $this->initConfig('..//Application/Home');
        $this->assertEquals('PRC', Config::get('default_timezone'));
        //在../Application/Home/configs.php中更新值为pdo.
        $this->assertEquals('mysql', Config::get('database.type'));
        $this->assertEquals('c', Config::get('session.a.b'));
        $this->assertEmpty(Config::get('session.a.b.'));
        $this->assertEmpty(Config::get('a'));
        $this->assertEmpty(Config::get('a.b'));
        $this->assertEmpty(Config::get('.a.b'));
        $this->assertArrayHasKey('suffix', Config::get());
    }
}
