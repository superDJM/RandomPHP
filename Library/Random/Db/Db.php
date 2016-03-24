<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 24/3/2016
 * Time: 下午9:11
 */

namespace Random\Db;


class Db
{
    protected $conn;

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