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
     * @var float 开始运行时间
     */
    static $startTime;

    /**
     * @var float 开始占用内存
     */
    static $startMem;


    /**
     * @author DJM <op87960@gmail.com>
     * @todo 记录开始数据
     */
    static function startCount()
    {
        self::$startTime = microtime(true);
        self::$startMem = memory_get_usage(true);
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 统计用量,并输出
     */
    static function endCount()
    {
        $usedTime = microtime(true) - self::$startTime;

        //转化ms显示
        $usedTime *= 1000;

        //取小数点4位
        $usedTime = number_format($usedTime, 4);

        $usedMem = memory_get_usage(true) - self::$startMem;

        //转化kb显示
        $usedMem /= 1024;

        echo "<br/><h2>usedTime: ", $usedTime, "ms usedMem: ", $usedMem, "kb </h2><br/>";
    }
}