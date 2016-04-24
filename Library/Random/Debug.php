<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午2:30
 */

namespace Random;

/**
 * Class Debug
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 用来输出一些脚本信息,如运行时间和占内存数
 */
class Debug
{

    /**
     * @var array 开始运行时间
     */
    private static $startTime = array();

    /**
     * @var array 开始占用内存
     */
    private static $startMem = array();


    /**
     * @author DJM <op87960@gmail.com>
     * @param $time bool 是否记录时间
     * @param $mem bool 是否记录内存
     * @todo 记录开始数据
     */
    static function startCount($time = true, $mem = true)
    {
        //根据传入参数判断是否开启记录
        ($time && function_exists('microtime')) ? self::$startTime[] = microtime(true) : false;
        ($mem && function_exists('memory_get_usage')) ? self::$startMem[] = memory_get_usage(true) : false;
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @param $extra string 额外的输出内容
     * @todo 统计用量,并输出
     */
    static function endCount($extra = '')
    {
        echo '<br/><span>', $extra, '</span>';

        if (self::$startTime && function_exists('microtime')) {
            $usedTime = microtime(true) - array_pop(self::$startTime);

            //转化ms显示
            $usedTime *= 1000;

            //取小数点4位
            $usedTime = round($usedTime, 4);
            echo '<h2>usedTime: ', $usedTime, 'ms</h2><br/>';
        }

        if (self::$startMem && function_exists('memory_get_usage')) {
            //根据大小转换单位
            $usedMem = self::convert(memory_get_usage(true) - array_pop(self::$startMem));

            echo '<h2>usedMem: ', $usedMem, '</h2><br/>';
        }
    }

    /**
     * @param $size
     * @return string
     * @author DJM <op87960@gmail.com>
     * @todo 转换大小为合适的单位
     */
    private static function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return ($size) ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i] : '0kb';
    }
}