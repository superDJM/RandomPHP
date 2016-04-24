<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25
 * Time: 16:49
 */

namespace Random;

/**
 * Class Orm
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo ORM类的实现
 */
class Orm
{
    /**
     * @var array 字段数据
     */
    protected $_data;

    /**
     * @var mixed 主键的值
     */
    protected $_pkVal = '';

    /**
     * @var bool 标记数据是否改变,改变则更新入库
     */
    protected $flag = false;

    /**
     * @var array 定义字段的类型
     */
    protected static $fields;

    /**
     * @var string 表名
     */
    protected static $table;

    /**
     * @var string 主键
     */
    protected static $pk = 'id';

    /**
     * @param $map array 查询条件
     * @return $this 对象
     * @author DJM <op87960@gmail.com>
     * @todo 返回一个对象
     */
    static function findOne($map)
    {
        return self::createOrmObj($map);
    }

    /**
     * @param $map array 查询条件
     * @return array 对象集
     * @author DJM <op87960@gmail.com>
     * @todo 查询对象集
     */
    static function findAll($map)
    {
        return self::createOrmObj($map, true);
    }

    /**
     * @return array
     * @author DJM <op87960@gmail.com>
     * @todo 返回对象的数据(数组)
     */
    public function getArray()
    {
        return $this->_data;
    }

    /**
     * @param $name
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo 魔术方法获取对象的成员的值
     */
    function __get($name)
    {
        return $this->_data[$name];
    }

    /**
     * @param $name
     * @param $value
     * @author DJM <op87960@gmail.com>
     * @todo 魔术方法设置对象的成员的值
     */
    function __set($name, $value)
    {
        if (isset($this->_data[$name])) {
            if ($value != $this->_data[$name]) {
                $this->flag = true;
            }
            $this->_data[$name] = $value;
        }
    }

    /**
     * @param $function
     * @param $arguments
     * @return array
     * @author DJM <op87960@gmail.com>
     * @todo 实现findByXXX(XXX为字段名)的查询,返回对象集
     */
    public static function __callStatic($function, $arguments)
    {
        if (self::checkArg($arguments[0]) && preg_match('/^findBy(\w*)$/', $function, $match)) {
            $name = $match[1];
            //数据库字段必须是全部小写，表名也要小写
            $map[strtolower($name)] = $arguments[0];
            return self::findAll($map);
        } else {
            trigger_error("$function 格式错误");
        }
    }

    /**
     * @param $arg
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 检查是否合法输入
     */
    private static function checkArg($arg)
    {
        return is_bool($arg) || is_numeric($arg) || is_string($arg);
    }

    /**
     * @param $map array 查询条件
     * @param $all bool 是否返回结果集
     * @return object Orm对象
     * @author DJM <op87960@gmail.com>
     * @todo 创建一个包含所查数组的对象
     */
    private static function createOrmObj($map, $all = false)
    {
        //获得类名
        $class = get_called_class();
        //解析表名
        !isset($class::$table) && ($class::$table = strtolower(substr($class, strrpos($class, '\\') + 1)));

        $str = '';

        //read
        $option['mode'] = 'r';
        $option['table'] = $class::$table;
        foreach ($map as $key => $val) {
            if (isset($class::$fields[$key])) {
                if (empty($str)) {
                    $str .= ' WHERE ';
                } else {
                    $str .= ' AND ';
                }
                $str .= "`$key` = :$key";
                $data[] = $key;
                $data[] = $val;
                $data[] = $class::$fields[$key];
                $option['param'][] = $data;
                unset($data);
            }
        }

        //构造占位sql
        $sql = 'SELECT * FROM `' . $class::$table . '`' . $str;

        /** @var  $obj Orm */
        $obj = new $class();

        //获取数据
        if ($all) {
            $res = Factory::getDatabase()->getArray($sql, $option);
            $obj_array = array();
            foreach ($res as $key => $value) {
                $obj_array[$key] = clone $obj;
                $obj_array[$key]->_data = $value;
                isset($value[$obj_array[$key]::$pk]) && $obj_array[$key]->_pkVal = $value[$obj_array[$key]::$pk];
            }
            unset($obj);
            $obj = $obj_array;
        } else {
            $res = Factory::getDatabase()->getRow($sql, $option);
            $obj->_data = $res;
            isset($res[$obj::$pk]) && $obj->_pkVal = $res[$obj::$pk];
        }

        //返回对象
        return $obj;
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 清除缓冲区,写入数据库
     */
    public function flush()
    {
        //数据改变,写库
        if ($this->flag) {
            $str = '';
            $option['mode'] = 'w';
            $option['table'] = static::$table;
            foreach ($this->_data as $key => $val) {
                if (!empty($str)) {
                    $str .= ',';
                }
                $str .= "`$key` = :$key";
                $data[] = $key;
                $data[] = $val;
                isset(static::$fields[$key]) && $data[] = static::$fields[$key];
                $option['param'][] = $data;
                unset($data);
            }
            //构造更新sql
            $sql = 'UPDATE `' . static::$table . '` SET ' . $str . ' WHERE `' . static::$pk . '` = ' . $this->_pkVal;

            Factory::getDatabase()->execute($sql, $option);
            $this->flag = false;
        }
    }


    public function __destruct()
    {
        $this->flush();
    }
}