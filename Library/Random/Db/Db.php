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
    public $conn;
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

    function commit()
    {

    }

    function rollback()
    {

    }

    function beginTransaction()
    {

    }

    function endTransaction()
    {

    }


    function query($sql)
    {
        if (empty($this->conn)) {
            $this->conn = $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
        }
        $result = $this->conn->query($sql);
        return $result;
    }

    function close()
    {
        $this->conn = null;
    }

    function __destruct()
    {
        $this->close();
    }
}