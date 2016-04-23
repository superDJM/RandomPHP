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
            $dsn = "$this->type:host=$host;dbname=$database;charset=utf8";
            $this->_conn = new \PDO($dsn, $username, $password);
            $this->_conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->_conn->exec('SET NAMES utf8');
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

    /**
     * @param $sql
     * @param $option
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo 根据传参来确定调用方法的参
     */
    private function getResult($sql, $option)
    {
        if (isset($option['param']) && isset($option['mode'])) {
            $result = $this->query($sql, $option['param'], $option['mode']);
        } else if (isset($option['param']) && !isset($option['mode'])) {
            $result = $this->query($sql, $option['param']);
        } else {
            $result = $this->query($sql);
        }
        return $result;
    }

    /**
     * @param $sql
     * @param array $option
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo 直接执行
     */
    public function execute($sql, $option = array())
    {
        $result = $this->getResult($sql, $option);
        return $result;
    }
    
    public function getRow($sql, $option = array())
    {
        $result = $this->getResult($sql, $option);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $arr = $result->fetch();
        return $arr;
    }

    public function getArray($sql, $option = array())
    {
        $result = $this->getResult($sql, $option);
        if ($result) {
            $result->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $result->fetchAll();
        }
        return $result;
    }
}
