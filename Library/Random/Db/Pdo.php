<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/24
 * Time: 14:44
 */

namespace Random\Db;

use Random\IDatabase;

class Pdo extends Db implements IDatabase
{
    /** @var $conn \PDO */
    protected $conn;

    function __construct($host, $username, $password, $database, $port = 3306){
        parent::__construct($host, $username, $password, $database, $port = 3306);
        $this->conn = $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
    }

    function connect($host, $username, $password, $database, $port=3306)
    {
        if (!isset($this->conn)) {
            $dsn = "mysql:host=$host;dbname=$database";
            $this->conn = new \PDO($dsn, $username, $password);
            if ($this->conn->errorCode() == '00000') {
                die($this->conn->errorInfo()[2]);
            }

        }
        return $this->conn;
    }

    public function getRow($sql){
        $result = $this->query($sql);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $arr = $result->fetch();
        return $arr;
    }

    public function getArray($sql){

        $result = $this->query($sql);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $arr = $result->fetchAll();
        return $arr;

    }
}
