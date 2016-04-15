<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午9:46
 */

namespace Random\Db;


class Mysqli extends Db
{

    function connect($host, $username, $password, $database, $port = 3306)
    {
        if (!isset($this->_conn)) {
            $this->_conn = new \mysqli($host, $username, $password, $database, $port);
            if ($this->_conn->errno) {
                die($this->_conn->error);
            }
            $this->_conn->set_charset('utf8');
        }
        return $this->_conn;
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @param $result mixed
     * @todo 请求后更新成员变量$_error等
     */
    protected function updateField($result)
    {
        $this->_affectedRows = $this->_conn->affected_rows;
        $this->_fieldCount = $this->_conn->field_count;
        $this->_insertId = $this->_conn->insert_id;
        $this->_error = $this->_conn->error;
        parent::updateField($result);
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 开启事务的mysqli实现
     */
    protected function begin_transaction()
    {
        //由于mysqli的begin_transaction方法需要php5.5以后,所以调用autocommit来解决
        return $this->_conn->autocommit(false);
    }

    public function getRow($sql)
    {
        $result = $this->query($sql);
        $arr = $result->fetch_assoc();
        return $arr;
    }

    public function getArray($sql)
    {

        $result = $this->query($sql);
        if ($result) {
            $result = $result->fetch_all(MYSQL_ASSOC);
        }

        return $result;
    }
}