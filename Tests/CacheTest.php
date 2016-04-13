<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/13
 * Time: 19:49
 */

namespace Test;
use Random\Config;
use Random\Factory;
use Random\DataCache;
use Random\Cache\FileCache;

/**
 * Class CacheTest
 * @package Test
 * @author qiming.c <qiming.c@foxmail.com>
 * @todo 缓存测试类
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function initConfig($path)
    {
        if(!defined('BASE_ROOT')){
            define('BASE_ROOT', '../');
        }
        Config::getInstance($path);
    }

    public function testFileCache()
    {
        $this->initConfig('..//Library/Random');
        $cache = Factory::getCache();
        $cache->set('a', 'aaaaaaaa', 3);
        $cache->set('b', array('a'=>'a', 'b'=>'b'), 3);
        $cache->set('c', json_encode(array('a'=>'a', 'b'=>'b')), 3);
        $cache->set('d', '<?php echo hello word; ?>');
        $this->assertEquals('aaaaaaaa', $cache->get('a'));
        $this->assertEquals(array('a'=>'a', 'b'=>'b'), $cache->get('b'));
        $this->assertEquals(json_encode(array('a'=>'a', 'b'=>'b')), $cache->get('c'));
        $this->assertEquals('<?php echo hello word; ?>', $cache->get('d'));
        $this->assertEmpty($cache->get('f'));
        $this->assertEmpty($cache->get('g'));
        sleep(3);
        $this->assertEmpty($cache->get('a'));
        $this->assertEmpty($cache->get('b'));
        $this->assertEmpty($cache->get('c'));
        $this->assertEquals('<?php echo hello word; ?>', $cache->get('d'));
    }
}