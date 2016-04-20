<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/24
 * Time: 14:44
 */

namespace Random\Db;

class Pdo extends Db
{

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param $database
     * @param int $port
     * @author DJM <op87960@gmail.com>
     * @todo 数据库连接
     */
    function connect($host, $username, $password, $database, $port = 3306)
    {
        if (!isset($this->_conn)) {
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8";
            $this->_conn = new \PDO($dsn, $username, $password);
            if ($this->_conn->errorCode() == '00000') {
                die($this->_conn->errorInfo()[2]);
            }
            $this->_conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->_conn->exec('set names utf8');
        }
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @param $result mixed
     * @todo 请求后更新成员变量$_error等
     */
    protected function updateField($result)
    {
        if ($result) {
            $this->_affectedRows = $result->rowCount();
            $this->_fieldCount = $result->columnCount();
        } else {
            $this->_affectedRows = 0;
            $this->_fieldCount = 0;
        }
        $this->_error = $this->_conn->errorInfo()['2'];
        $this->_insertId = $this->_conn->lastInsertId();
        parent::updateField($result);
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 开启事务的pdo实现
     */
    protected function begin_transaction()
    {
        return $this->_conn->beginTransaction();
    }

    public function getRow($sql, $param = array())
    {
        $result = $this->query($sql, $param);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $arr = $result->fetch();
        return $arr;
    }

    public function getArray($sql, $param = array())
    {

        $result = $this->query($sql, $param);
        if ($result) {
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $result->fetchAll();
        }
        return $result;
    }
}
