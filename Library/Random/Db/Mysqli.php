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
            $this->conn = new \mysqli($host, $username, $password, $database, $port);
            if (!empty($this->conn->error)) {
                die($this->conn->error);
            }
        }
        return $this->conn;
    }



    public function getArray($sql){

        $result = $this->query($sql);
        $arr = $result->fetch_all(MYSQL_ASSOC);
        return $arr;
    }

}