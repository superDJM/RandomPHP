<?php
/**
 * Created by PhpStorm.
 * User: qiming.c
 * Date: 2016/3/25
 * Time: 21:28
 */
/*
 * use:
 *
 * $sql = new SqlBuilder('user');
 * $sql->where("id=1")->limit(2,4)->order("id DESC")->select("id")->buildsql();
 * $sql->where("name='小铭'")->limit(4)->order("id DESC")->select("id, name, age")->buildsql();
 * $sql->where("name='A'")->select("*")->buildsql();
 * $sql->update(array('name'=>'A', 'age'=>15))->where("id=1")->buildsql();
 * $sql->add(array('id' => 1, 'name'=>'H', 'age'=>15))->buildsql();
 */
namespace Random;


class SqlBuilder
{
    private $_where  = '';
    private $_order  = '';
    private $_limit  = '';
    private $_select = '';
    private $_insert = '';
    private $_update = '';
    private $_table  = '';
    private $_pk     = '';

    public function __construct($table){
        $this->_table = $table;
    }

    public function where($where="1=1"){
        $this->_where = " WHERE ".$where;
        return $this;
    }

    public function order($order="id DESC"){
        $this->_order = " ORDER BY ".$order;
        return $this;
    }

    public function limit($limit1="20", $limit2=""){
        if ($limit2) {
            $this->_limit = " LIMIT ".$limit1.", ".$limit2;
        } else {
            $this->_limit = " LIMIT 0, ".$limit1;
        }
        return $this;
    }

    public function select($select="*"){
        $this->_select = "SELECT ".$select." FROM ".$this->_table;
        return $this;
    }

    public function buildsql(){
        if ($this->_select){
            $sql =  $this->_select.$this->_where.$this->_order.$this->_limit;
        } elseif($this->_update) {
            $sql =  $this->_update.$this->_where;
        } elseif($this->_insert){
            $sql = $this->_insert;
        }
        $this->resetargs();
        return $sql;
    }

    public function add($value=array()){
        $this->_insert = "INSERT INTO $this->_table";
        $keys = '';
        $vals = '';
        foreach ($value as $key => $val) {
            $keys .= $key.",";
            $vals .= $this->numorstr($val).",";
        }
        $keys = rtrim($keys, ',');
        $vals = rtrim($vals, ',');
        $this->_insert .= "($keys)" . " VALUES" ."($vals)";
        return $this;
    }


    public function update($value = array()){
        $this->_update = "UPDATE $this->_table set ";
        foreach ($value as $key => $val) {
            $this->_update .= " $key=".$this->numorstr($val).",";
        }
        $this->_update = rtrim($this->_update, ',');
        return $this;
    }


    public function resetargs(){
        $this->_where  = '';
        $this->_order  = '';
        $this->_limit  = '';
        $this->_select = '';
        $this->_update = '';
        $this->_insert = '';
    }

    public function fields(){
        $database = Factory::getDatabase();
        $database->query("show  COLUMNS FROM $this->_table");
    }

    public function getPrimaryKey(){
        return $this->_pk;
    }

    // 若是str 则加上''
    public function numorstr($val){
        if (is_numeric($val)){
            return $val;
        } else {
            return "'$val'";
        }
    }
}