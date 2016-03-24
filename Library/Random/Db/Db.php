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
        $this->conn = $this->connect($host, $username, $password, $database, $port);
    }
}