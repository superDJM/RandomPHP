<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/25
 * Time: 16:49
 */

namespace Random;

use Random\Db\Sql;

class Orm
{
    //所有字段
    protected $data;

    public static function findOne($map){
        $sql_bulid = new Sql();
        //数据库字段必须是全部小写，表名也要小写
        $sql = $sql_bulid->from(strtolower(__CLASS__))->where($map)->limit(1)->sqlbulid();
        $database = Factory::getDatabase();
        $arr = $database->getRow($sql);
        $class = __CLASS__;
        $obj = new $class();
        $obj->data = $arr;
        return $obj;
    }

    public static function findAll($map){
        $sql_build = new Sql();
        //数据库字段必须是全部小写，表名也要小写
        $sql = $sql_build->from(strtolower(__CLASS__))->where($map)->sqlbuild();
        $database = Factory::getDatabase();
        $arr = $database->getArray($sql);
        $obj_array = array();
        $class = __CLASS__;
        foreach($arr as $key=>$value){
            $obj_array[$key] = new $class();
            $obj_array[$key]->data = $value;
        }
        return $obj_array;
    }

    public function getArray(){
        return $this->data;
    }

    function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->data[$name];
    }

    function __set($name, $value)
    {
        // TODO: Implement __set() method.
        if(isset($this->data[$name])){
            $this->data[$name] = $value;
        }
    }

    function __callStatic($function, $arguments)
    {
        // TODO: Implement __callStatic() method.
        $name = substr($function,7);
        //数据库字段必须是全部小写，表名也要小写
        if(isset($this->data[strtolower($name)])){
            $map[strtolower($name)] = $arguments[0];
            $class = __CLASS__;
            return $class::findAll($map);
        }else{
            return null;
        }
    }


}