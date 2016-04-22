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
    //所有字段
    private $_data;
    private $_model;

    /**
     * Orm constructor.
     * @param $table
     * @todo 初始化一个model类
     */
    public function __construct($table)
    {
        $this->_model = new Model(strtolower($table));
    }

    /**
     * @param $map array 查询条件
     * @return $this 对象
     * @author DJM <op87960@gmail.com>
     * @todo 返回一个对象
     */
    public function findOne($map)
    {
        $arr = $this->_model->where($map)->limit(1)->select()->getOne();
        $this->_data = $arr;
        return $this;
    }

    /**
     * @param $map array 查询条件
     * @return array 对象集
     * @author DJM <op87960@gmail.com>
     * @todo 查询对象集
     */
    public function findAll($map)
    {
        $arr = $this->_model->where($map)->select()->getAll();
        $obj_array = array();
        foreach($arr as $key=>$value){
            $obj_array[$key] = clone $this;
            $obj_array[$key]->_data = $value;
        }
        return $obj_array;
    }

    public function getArray(){
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
        // TODO: Implement __get() method.
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
        // TODO: Implement __set() method.
        if (isset($this->_data[$name])) {
            $this->_data[$name] = $value;
        }
    }

    /**
     * @param $function
     * @param $arguments
     * @return array
     * @author DJM <op87960@gmail.com>
     * @todo 实现findXXX(XXX为字段名)的查询,返回对象集
     */
    public function __call($function, $arguments)
    {
        if (preg_match('/^find(.*)/', $function, $match)) {
            $name = $match[1];
            //数据库字段必须是全部小写，表名也要小写
            $map[strtolower($name)] = $arguments[0];
            return $this->findAll($map);
        } else {
            trigger_error("$function 格式错误");
        }
    }


}