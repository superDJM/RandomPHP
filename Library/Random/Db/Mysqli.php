<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午9:46
 */

namespace Random\Db;


use Random\IDatabase;

class Mysqli implements IDatabase
{

    protected $conn;

    function connect($host, $username, $password, $database, $port)
    {
        if (!isset($this->conn)) {
            $this->conn = new \mysqli($host, $username, $password, $database, $port);
            if (!empty($this->conn->error)) {
                die($this->conn->error);
            }
        }
        return $this->conn;
    }

    function query($sql)
    {
        $this->connect();
        $result = $this->conn->query($sql);
        return $result;
    }

    function close()
    {
        unset($this->conn);
    }

    function __destruct()
    {
        $this->close();
    }

}