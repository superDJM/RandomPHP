<?php
/**
 * Created by PhpStorm.
 * User: qiming.c
 * Date: 2016/3/27
 * Time: 21:28
 */
/*
 * Example:
 * $sql = new SqlBuilder('user');
 * $sql->where(array('id'=>1, 'name'=>'A'))->select()->buildSql();
 * $sql->update(array('name'=>'A' AND 'age'=>15))->where("id=1")->buildSql();
 * $sql->add(array('id' => 1, 'name'=>'H', 'age'=>15))->buildSql();
 * $sql->delete()->where('id=1')->buildSql();
 * $sql->getFieids();
 * $sql->getPrimaryKey();
 */
namespace Random;
use Random\Factory;

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
    protected $_handle ;
    protected $_fields = array();

    public function __construct($table){
        $this->_table = "`".$table."`";
        $this->_handle = Factory::getDatabase();
        $data = $this->_handle->getArray("show  COLUMNS FROM user");
        foreach ($data as $arr) {
            $this->_fields[$arr['Field']]=$arr['Type'];
            if ($arr['Key']){
                $this->_fields['_pk'] = $arr['Field'];
            }
        }
    }

    /**
     * 
     * @access public
     * @param  String $where
     * @param  Array $vals
     * @return $this
     * @example  where("name='A'") Or where("name=%s", array('A')) Or where(array('id'=>'1', 'name'=>'A'))
     */
    public function where($where="1=1",$vals=null){
        if ($vals==null && is_string($where)){
            $this->_where = " WHERE ".$where;
        } elseif (is_string($where) && is_array($vals)){
            $vals = array_map(array($this, "check"), $vals);
            $this->_where = " WHERE ".vsprintf($where,$vals);
        }elseif (is_array($where)){
            $this->_where = " WHERE ";
            foreach ($where as $key => $value) {
                $this->_where .= " `$key` = ".$this->checkValue($key, $value)." AND";
            }
            $this->_where = substr($this->_where,0,strlen($this->_where)-3);
        }
        return $this;
    }

    public function order($order="id DESC"){
        $this->_order = " ORDER BY ".$order;
        return $this;
    }

    public function limit($offset="20", $length=null){
        if ($length) {
            $this->_limit = " LIMIT ".$offset.", ".$length;
        } else {
            $this->_limit = " LIMIT 0, ".$offset;
        }
        return $this;
    }

    /**
     * @param  String Or Array
     * @example  select(array('id', 'name', 'age') Or select("id, name, age")
     * @return $this
     */
    public function select($select="*"){
        if (is_array($select)){
            $select = array_map(function($se){return '`'.$se.'` ';}, $select);
            $this->_select = "SELECT ".implode($select)." FROM ".$this->_table;
        } else {
            $this->_select = "SELECT ".$select." FROM ".$this->_table;
        }       
        return $this;
    }

    /**
     * @access public
     * @return sql语句
     * @todo 构建sql语句
     */
    public function buildSql(){
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

    /**
     * 
     * @access public 
     * @param  Array $value
     * @example  add(array('id' => 1, 'name'=>'H', 'age'=>15))
     * @return $this
     */
    public function add($value=array()){
        $this->_insert = "INSERT INTO $this->_table";
        $keys = '';
        $vals = '';
        foreach ($value as $key => $val) {
            $keys .= "`".$key."`,";
            $vals .= $this->checkValue($key, $val).",";
        }
        $keys = rtrim($keys, ',');
        $vals = rtrim($vals, ',');
        $this->_insert .= "($keys)" . " VALUES" ."($vals)";
        return $this;
    }

    /**
     * 
     * @access public
     * @param  Array $value
     * @example  update(array('name'=>'A', 'age'=>15))
     * @return $this
     */
    public function update($value = array()){
        $this->_update = "UPDATE $this->_table set ";
        foreach ($value as $key => $val) {
            $this->_update .= " `$key`=".$this->checkValue($key, $val).",";
        }
        $this->_update = rtrim($this->_update, ',');
        return $this;
    }

    public function group($group){
        $this->_group = " GROUP BY `".$group."`";
        return $this;
    }

    /**
     * 
     * @access public
     * @example  delete()->where('id=1')
     * @return $this
     */ 
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

    /**
     * @return Array(字段名=>类型)
     */
    public function getFields(){
        return $this->_fields;
    }

    /**
     * @return 主键的字段名
     */
    public function getPrimaryKey(){
       return $this->_fields['_pk'];
   }

    public function __destruct(){

    }

    public function checkValue($key, $val){
        $field = $this->_fields[$key];
        if ((substr($field, 0, 3) == 'int') && (!is_numeric($val))) {
            $val = (int)($val);
        }
        return $this->check($val);
    }

    public function check($val){
        $val = mysqli_real_escape_string($this->_handle->conn, $val);
        if (is_numeric($val)){
            return $val;
        } else {
            return "'$val'";
        }
    }
}

