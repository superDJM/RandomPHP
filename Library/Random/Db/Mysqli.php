<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午9:46
 */

namespace Random\Db;


use Random\IDatabase;

class Mysqli extends Db implements IDatabase
{
    /** @var  $conn \mysqli */
    protected $conn;

    function connect($host, $username, $password, $database, $port=3306)
    {
        if (!isset($this->conn)) {
            if($host == null){
                $host = $this->host;
            }
            if($username == null){
                $username = $this->username;
            }
            if($password == null){
                $password = $this->password;
            }
            if($database == null){
                $database = $this->database;
            }
            $this->conn = new \mysqli($host, $username, $password, $database, $port);
            if (!empty($this->conn->error)) {
                die($this->conn->error);
            }
        }
        return $this->conn;
    }

    public function getRow($sql){
        $result = $this->query($sql);
        $arr = $result->fetch_assoc();
        return $arr;
    }

    public function getArray($sql){

        $result = $this->query($sql);
        $arr = $result->fetch_all(MYSQL_ASSOC);
        return $arr;
    }

}