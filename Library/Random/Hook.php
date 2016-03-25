<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午2:26
 */

namespace Random;

/**
 * Class Hook
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 钩子类,用观察者模式实现(tp)
 */
class Hook
{
    private static $tags = [];

    /**
     * 动态添加行为扩展到某个标签
     * @param string $tag 标签名称
     * @param mixed $behavior 行为名称
     * @param bool $first 是否放到开头执行
     * @return void
     */
    public static function add($tag, $behavior, $first = false)
    {
        if (!isset(self::$tags[$tag])) {
            self::$tags[$tag] = [];
        }
        if (is_array($behavior)) {
            self::$tags[$tag] = array_merge(self::$tags[$tag], $behavior);
        } elseif ($first) {
            array_unshift(self::$tags[$tag], $behavior);
        } else {
            self::$tags[$tag][] = $behavior;
        }
    }

    /**
     * 获取插件信息
     * @param string $tag 插件位置 留空获取全部
     * @return array
     */
    public static function get($tag = '')
    {
        if (empty($tag)) {
            // 获取全部的插件信息
            return self::$tags;
        } else {
            return self::$tags[$tag];
        }
    }

    /**
     * 监听标签的行为
     * @param string $tag 标签名称
     * @param mixed $params 传入参数
     * @return void
     */
    public static function listen($tag, &$params = null)
    {
        if (isset(self::$tags[$tag])) {
            foreach (self::$tags[$tag] as $name) {

                $result = self::exec($name, $tag, $params);

                if (false === $result) {
                    // 如果返回false 则中断行为执行
                    return;
                }
            }
        }
        return;
    }

    /**
     * 执行某个行为
     * @param string $class 行为类名称
     * @param string $tag 方法名（标签名）
     * @param Mixed $params 传人的参数
     * @return mixed
     */
    public static function exec($class, $tag = '', &$params = null)
    {
        if ($class instanceof \Closure) {
            return $class($params);
        }
        $obj = new $class();
        return ($tag && is_callable([$obj, $tag])) ? $obj->$tag($params) : $obj->run($params);
    }
}