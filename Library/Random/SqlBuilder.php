<?php
/**
 * Created by PhpStorm.
 * User: qiming.c
 * Date: 2016/3/26
 * Time: 21:28
 */
/*
 * Example:
 * $sql = new SqlBuilder('user');
 * $sql->where("id=1")->limit(2,4)->order("id DESC")->select("id")->buildsql();
 * $sql->update(array('name'=>'A', 'age'=>15))->where("id=1")->buildsql();
 * $sql->add(array('id' => 1, 'name'=>'H', 'age'=>15))->buildsql();
 * $sql->delete()->where('id=1')->buildsql();
 */
namespace Random;


class SqlBuilder
{
    protected $_where  = '';
    protected $_order  = '';
    protected $_limit  = '';
    protected $_select = '';
    protected $_insert = '';
    protected $_update = '';
    protected $_table  = '';
    protected $_group  = '';
    protected $_delete = '';

    public function __construct($table){
        $this->_table = $table;
    }

    /**
     *
     * @access public
     * @author qiming.c
     * @param  String $where
     * @param  Array $vals
     * @return $this
     * @example  where("name='A'") Or where("name=%s", array('A'))
     */
    public function where($where="1=1",$vals=null){
        if ($vals==null && is_string($where)){
            $this->_where = " WHERE ".$where;
        } elseif (is_string($where) && is_array($vals)){
            $vals = array_map(array($this, "numorstr"), $vals);
            $this->_where = " WHERE ".vsprintf($where,$vals);
        }
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
            $sql =  $this->_select.$this->_where.$this->_group.$this->_order.$this->_limit;
        } elseif($this->_update) {
            $sql =  $this->_update.$this->_where;
        } elseif($this->_insert){
            $sql = $this->_insert;
        } elseif ($this->_delete){
            $sql = $this->_delete.$this->_where;
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

    public function group($group){
        $this->_group = " GROUP BY ".$group;
        return $this;
    }

    public function delete(){
        $this->_delete = "DELETE "." FROM ".$this->_table;
        return $this;
    }


    public function resetargs(){
        $this->_where  = '';
        $this->_order  = '';
        $this->_limit  = '';
        $this->_select = '';
        $this->_update = '';
        $this->_insert = '';
        $this->_group  = '';
        $this->_delete = '';
    }

//     public function fields(){
//         $database = Factory::getDatabase();
//         return $database->getArray("show  COLUMNS FROM $this->_table");
//     }
//
//    public function getPrimaryKey(){
//        return $this->_pk;
//    }
//
//     若是str 则加上''
    public function numorstr($val){
        if (is_numeric($val)){
            return $val;
        } else {
            return "'$val'";
        }
    }
}