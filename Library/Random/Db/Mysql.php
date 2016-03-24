<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/24
 * Time: 13:45
 */

namespace Random\Db;

use Random\IDatabase;

class Mysql implements IDatabase
{
    protected $conn;

    function connect($host, $username, $password, $database, $port)
    {
        $this->conn = mysql_connect($host,$username,$password) or die("Unable to connect to the MySQL!");
        mysql_select_db($database,$this->conn);
        return $this->conn;
    }

    function query($sql)
    {
        if(empty($this->conn)){
            die("no connect");
        }
        $result=mysql_query($sql);
        //$row=mysql_fetch_row($result);
        return $result;
    }

    function close()
    {
        mysql_close($this->conn);
    }

    function __destruct()
    {
        $this->close();
    }
}