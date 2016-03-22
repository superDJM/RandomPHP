<?php
namespace Random;

/**
 * Class Register
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 注册树类,提供各种对象存取
 */
class Register
{
    protected static $objects;

    static function set($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    static function get($key)
    {
        if (!isset(self::$objects[$key])) {

//            $method = 'get' . ucwords($key);
//            //尝试去调用工厂方法
//            if(method_exists('Random\\Factory',$method)){
//                $obj = Factory::$method();
//                self::set($key,$obj);
//            }else{
            return false;
//            }
        }
        return self::$objects[$key];
    }

    function _unset($alias)
    {
        unset(self::$objects[$alias]);
    }
}