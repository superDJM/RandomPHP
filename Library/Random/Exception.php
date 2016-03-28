<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午2:30
 */

namespace Random;


class Exception extends \Exception
{

    public static function Register()
    {
        set_exception_handler(array(__CLASS__, 'exception_handler'));
        set_error_handler(array(__CLASS__, 'exception_error_handle'));
    }

    static function exception_handler($exception)
    {
        ob_clean();
        ob_start();
        echo "Uncaught exception: ", $exception->getMessage(), "\n";
        ob_end_flush();
        exit;
    }

    static function exception_error_handle($errno, $errmsg, $filename, $linenum, $vars)
    {
        die($errmsg);
    }
}