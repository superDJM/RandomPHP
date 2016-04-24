<?php
/**
 * Created by PhpStorm.
 * User: qiming.c <qiming.c@foxmail.com>
 * Date: 2016/4/8
 * Time: 17:51
 */
/*
 * Example:
 *    $sql->select(array('id', 'name', 'age'))->buildSql();
 *    $sql->where(array('id'=>1, 'name'=>'A'))->select()->buildSql();
 *    $sql->update(array('name'=>'A', 'age'=>15))->where("id=1")->buildSql();
 *    $sql->add(array('id' => 16, 'name'=>'H', 'age'=>15))->buildSql();
 *    $sql->delete()->where(array('name'=>'A'))->buildSql();
 *    $sql->select()->join('TABLE ON ...')->buildSql();
 */
namespace Random;

class SqlBuilder
{
    private $_error  = '';
    private $_where  = '';
    private $_order  = '';
    private $_limit  = '';
    private $_select = '';
    private $_insert = '';
    private $_update = '';
    private $_table  = '';
    private $_group  = '';
    private $_delete = '';
    private $_count  = '';
    private $_join   = '';
    private $_param  = null;
    private $_fields = array();
    protected $_type = '';
    protected $_handle ;


    public function __construct($table)
    {
        $this->_table = "`".$table."`";
        $this->_handle = Factory::getDatabase();
        $this->_type = Config::get('database')['type'];
        $data = $this->_handle->getArray("SHOW COLUMNS FROM ".$this->_table);
        foreach ($data as $arr) {
            preg_match('/(^[a-z]+)\(.*/',$arr['Type'], $match);
            $this->_fields[$arr['Field']]=$match[1];
            if ($arr['Key']=='PRI'){
                $this->_fields['_pk'] = $arr['Field'];
            }
        }
    }

    /**
     *
     * @access public
     * @param  mixed $where
     * @return $this
     * @example  where("name='A'") Or where(array('id'=>'1', 'name'=>'A'))
     */
    public function where($where="1=1")
    {
        if (is_string($where)) {
            $this->_where      = " WHERE";
            $str_split_by_or   = preg_split('/\sor\s/i', $where);
            $array_split_by_or = array_map(array($this, 'str2arr'), $str_split_by_or);
            $array_split_by_or = array_map(array($this, 'checkArrayVal'), $array_split_by_or);
            array_map(function($array){
                foreach ($array as $key => $value) {
                    $this->_where .= " `$key` = ".$value." AND";
                }
                $this->_where = substr($this->_where,0,strlen($this->_where)-4);
                $this->_where .= ' OR';
            }, $array_split_by_or);
            $this->_where = substr($this->_where,0,strlen($this->_where)-3);

        } else if (is_array($where)) {
            $this->_where = " WHERE";
            $where = $this->checkArrayVal($where);
            foreach ($where as $key => $value) {
                $this->_where .= " `$key` = ".$value." AND";
            }
            $this->_where = substr($this->_where,0,strlen($this->_where)-4);

        } else {
            $this->_where = '';
        }
        return $this;
    }

    public function order($order="id DESC")
    {
        $this->_order = " ORDER BY ".$order;
        return $this;
    }

    public function limit($offset="20", $length=null)
    {
        if ($length) {
            $this->_limit = " LIMIT ".$offset.", ".$length;
        } else {
            $this->_limit = " LIMIT 0, ".$offset;
        }
        return $this;
    }

    /**
     * @param  mixed $select
     * @example  select(array('id', 'name', 'age') Or select("id, name, age")
     * @return $this
     */
    public function select($select="*")
    {
        if (is_array($select)) {
            $select = $this->checkField($select);
            $select = array_map(function($se){return '`'.$se.'`,';}, $select);
            if (empty($select)) $select=array('*');
            $this->_select = "SELECT ".rtrim(implode($select), ',')." FROM ".$this->_table;
        } elseif ($select == '*') {
            $this->_select = "SELECT ".$select." FROM ".$this->_table;
        } else {
            $selectArray = explode(',', $select);
            $selectArray = array_map('trim', $selectArray);
            $selectArray = $this->checkField($selectArray);
            $select = '`'.implode('`, `', $selectArray).'`';
            if (empty($select)) $select='*';
            $this->_select = "SELECT ".$select." FROM ".$this->_table;
        }
        return $this;
    }

    /**
     * @return array('sql'=>sql语句, 'option'=>array('param'=>参数数组, 'mode'=>读写类型, 'table'=>表名))
     * @todo 构建sql语句
     */
    public function buildSql()
    {
        $return['option']['param'] = $this->_param;
        $sql = $this->getSql();
        if (preg_match('/INSERT/i', $sql) || preg_match('/UPDATE/i', $sql)) {
            $return['option']['mode'] = 'w';
        } else {
            $return['option']['mode'] = 'r';
        }
        $return['option']['table'] = trim($this->_table, '`');
        $return['sql'] = $sql;
        return $return;
    }

    /**
     * @param  array $value
     * @example  add(array('id' => 1, 'name'=>'H', 'age'=>15))
     * @return $this
     */
    public function add($value=array())
    {
        $value = $this->checkArrayVal($value);
        $this->_insert = "INSERT INTO $this->_table";
        $this->_insert .= " (`".implode('`, `', array_keys($value))."`) VALUES (".implode(', ', array_values($value)).")";
        return $this;
    }

    /**
     * @param  array $value
     * @example  update(array('name'=>'A', 'age'=>15))
     * @return $this
     */
    public function update($value = array())
    {
        $this->_update = "UPDATE $this->_table set";
        $value = $this->checkArrayVal($value);
        foreach ($value as $key => $val) {
            $this->_update .= " `$key`=".$val.",";
        }
        $this->_update = rtrim($this->_update, ',');
        return $this;
    }

    public function group($group)
    {
        $this->_group = " GROUP BY ".$group;
        return $this;
    }

    /**
     * @example  delete()->where('id=1')
     * @return $this
     */
    public function delete()
    {
        $this->_delete = "DELETE "."FROM ".$this->_table;
        return $this;
    }

    /**
     * @example $Object->count()->where()->buildSql()
     * @return $this
     * @todo 统计语句
     */
    public function count($count='*')
    {
        $this->_count = 'SELECT COUNT('.$count.') FROM '.$this->_table;
        return $this;
    }

    /**
     * @param $join
     * @param $style
     * @example $Object->select()->join('TABLE ON ...')->buildSql()
     * @return $this
     * @todo 跨表操作
     */
    public function join($join, $style='INNER')
    {
        $this->_join = ' '.$style.' JOIN '.$join;
        return $this;
    }

    /**
     * @todo 获取字段信息
     * @return array (字段名=>类型)
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @return string 主键的字段名
     */
    public function getPrimaryKey()
    {
        return $this->_fields['_pk'];
    }

    /**
     * @todo sql语句的检测
     */
    protected function checkSql()
    {
        if ($this->_select && $this->_update) {
            $this->_error.="Error:不能同时使用select和update ";
        } elseif ($this->_select && $this->_insert) {
            $this->_error.="Error:不能同时使用select和add ";
        } elseif ($this->_select && $this->_delete) {
            $this->_error.="Error:不能同时使用select和delete ";
        } elseif ($this->_update && $this->_insert) {
            $this->_error.="Error:不能同时使用update和add ";
        } elseif ($this->_delete && $this->_update) {
            $this->_error.="Error:不能同时使用update和delete ";
        } elseif ($this->_insert && $this->_delete) {
            $this->_error.="Error:不能同时使用insert和delete ";
        }
    }

    protected function resetargs()
    {
        $this->_where  = '';
        $this->_order  = '';
        $this->_limit  = '';
        $this->_select = '';
        $this->_update = '';
        $this->_insert = '';
        $this->_group  = '';
        $this->_delete = '';
        $this->_error  = '';
        $this->_count  = '';
        $this->_join   = '';
        $this->_param  = null;
    }

    /**
     * @param $array
     * @return array 处理过的数组
     * @todo 对array参数进行检测和处理.
     */
    protected function checkArrayVal($array=array())
    {
        $array = $this->checkField($array);
        $resultArray = array_map(array($this, 'checkValue'), array_keys($array), array_values($array));
        $resultArray = array_combine(array_keys($array), array_values($resultArray));
        return $resultArray;
    }

    /**
     * @param $array
     * @return array
     * @todo 字段检测,去除不存在字段
     */
    protected function checkField($array)
    {
        $returnArray = array();
        if (!($this->is_assoc($array))) {
            foreach ($array as $key => $value) {
                if (in_array($value, array_keys($this->_fields)))
                    $returnArray[$key] = $value;
            }
        } else {
            foreach ($array as $key => $value) {
                if (in_array($key, array_keys($this->_fields)))
                    $returnArray[$key] = $value;
            }
        }
        return $returnArray;
    }

    /**
     * @param  $key
     * @param $val
     * @return string
     * @todo 对参数进行强制转换和转义
     */
    protected function checkValue($key, $val)
    {
        $field = $this->_fields[$key];
        if ((substr($field, 0, 3) == 'int') && (!is_int($val))) {
            $val = (int)($val);
        } elseif ((substr($field, 0, 3) == 'flo') && (!is_float($val))) {
            $val = (float)($val);
        } elseif ((substr($field, 0, 3) == 'dou') && (!is_double($val))) {
            $val = (double)($val);
        }
        $placeholder = ':'.$key;
        $param[] = $key;
        $param[] = $val;
        $param[] = $this->_fields[$key];
        $this->_param[] = $param;
        return $placeholder;
    }

    // /**
    //  * @param $val
    //  * @todo 对数据进行转义处理
    //  * @return string
    //  */
    // protected function check($val)
    // {
    //     if (is_string($val)) {
    //         if ($this->_type=='mysqli') {
    //             $val = $this->_handle->getConnection()->real_escape_string($val);
    //         } else {
    //             if ( !get_magic_quotes_gpc() ){
    //                 $val = addslashes($val);
    //             } 
    //         }
    //         return "'$val'";
    //     } else {
    //         return $val;
    //     }
    // }

    /**
     * @param  $arr
     * @return boolean
     * @todo 判断是否关联数组
     */
    protected function is_assoc($arr=array())
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function getSql()
    {
        $this->checkSql();
        $sql = null;
        if ($this->_error) {
            trigger_error($this->_error);
            $this->resetargs();
            return $sql;
        } elseif ($this->_select) {
            $sql =  $this->_select.$this->_join.$this->_where.$this->_group.$this->_order.$this->_limit;
        } elseif($this->_update) {
            $sql =  $this->_update.$this->_where;
        } elseif($this->_insert) {
            $sql = $this->_insert;
        } elseif ($this->_delete) {
            $sql = $this->_delete.$this->_where;
        } elseif ($this->_count) {
            $sql = $this->_count.$this->_join.$this->_where;
        }
        $this->resetargs();
        return $sql;
    }

    /**
     * @param string $str
     * @return array 参数数组
     * @todo 处理以string形式传入的where语句
     */
    protected function str2arr($str)
    {
        $return = array();
        $array1 = preg_split('/\sand\s/i', $str);
        $array1 = array_map('trim', $array1);
        $array2 = array_map(function($param){
            return explode('=', $param);
        }, $array1);
        foreach ($array2 as $arr) {
            $arr[1] = trim($arr[1]);
            $arr[1] = trim($arr[1], '\'');
            $arr[1] = trim($arr[1], "\"");
            $return[trim($arr[0])] = $arr[1];
        }
        return $return;
    }
}