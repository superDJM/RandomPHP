<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 24/3/2016
 * Time: 下午9:11
 */

namespace Random\Db;

use Random\IDatabase;

class Db implements IDatabase
{
    protected $_conn;
    protected $transactionCounter = 0;
    protected $transactionError = array();
    protected $_error;
    protected $_insertId;
    protected $_affectedRows;
    protected $_fieldCount;

    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $port;

    function __construct($host, $username, $password, $database, $port = 3306)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->_insertId;
    }

    /**
     * @return mixed
     */
    public function getAffectedRows()
    {
        return $this->_affectedRows;
    }

    /**
     * @return mixed
     */
    public function getFieldCount()
    {
        return $this->_fieldCount;
    }

    function connect($host, $username, $password, $database, $port)
    {
    }

    public function getArray($sql)
    {
    }

    public function getRow($sql)
    {
    }

    public function getConnection()
    {
        $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
        return $this->_conn;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 事务commit
     */
    function commit()
    {
        if (!--$this->transactionCounter) {
            return $this->_conn->commit();
        }
        return $this->transactionCounter >= 0;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 事务回滚
     */
    function rollback()
    {
        if (--$this->transactionCounter) {
            $this->query('ROLLBACK TO trans' . ($this->transactionCounter + 1));
            return true;
        }
        return $this->_conn->rollback();
    }

    /**
     * @return bool|void
     * @author DJM <op87960@gmail.com>
     * @todo 开启事务
     */
    function beginTransaction()
    {
        if (!$this->transactionCounter++) {
            $this->transactionError["'$this->transactionCounter'"] = false;
            return $this->begin_transaction();
        }
        $this->query('SAVEPOINT trans' . $this->transactionCounter);
        $this->transactionError["'$this->transactionCounter'"] = false;
        return $this->transactionCounter >= 0;
    }

    /**
     * @return bool
     * @author DJM <op87960@gmail.com>
     * @todo 结束事务
     */
    function endTransaction()
    {
        if ($this->transactionError["'$this->transactionCounter'"]) {
            //把事务的错误赋值给_error,用getError()获取
            $this->_error = $this->transactionError["'$this->transactionCounter'"];
            //销毁这个变量
            unset($this->transactionError["'$this->transactionCounter'"]);
            $result = $this->rollback();
        } else {
            $result = $this->commit();
        }
        //如果是mysqli驱动则还需要调用这个方法去恢复自动提交模式
        if (!$this->transactionCounter && strpos(get_called_class(), 'Mysqli')) {
            $this->_conn->autocommit(true);
        }
        return $result;
    }

    protected function begin_transaction()
    {

    }

    protected function updateField($result)
    {
        if ($this->transactionCounter && !$result) {
            $this->transactionError["'$this->transactionCounter'"] = $this->_error;
        }
    }

    /**
     * @param $sql
     * @return mixed
     * @author DJM <op87960@gmail.com>
     * @todo sql查询
     */
    function query($sql)
    {
        if (empty($this->_conn)) {
            $this->_conn = $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
        }
        $result = $this->_conn->query($sql);
        $this->updateField($result);
        return $result;
    }

    /**
     * @author DJM <op87960@gmail.com>
     * @todo 关闭连接
     */
    function close()
    {
        $this->_conn = null;
    }

    function __destruct()
    {
        $this->close();
    }
}