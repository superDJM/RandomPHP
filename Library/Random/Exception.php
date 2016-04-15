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

    /**
     * @var bool debug模式开关
     */
    private static $_debug;
    /**
     * @var integer 错误文件显示偏移量
     */
    private static $lineOffset;
    

    /**
     * @param $debug bool
     * @param $lineOffset integer
     * @author DJM <op87960@gmail.com>
     * @todo 注册错误函数回调方法
     */
    public static function Register($debug, $lineOffset)
    {
        self::$_debug = $debug;
        self::$lineOffset = $lineOffset;
        register_shutdown_function(array(__CLASS__, 'shutdown_handler'));
        set_exception_handler(array(__CLASS__, 'exception_handler'));
        set_error_handler(array(__CLASS__, 'exception_error_handle'));
    }

    static function shutdown_handler()
    {
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    $errMsg = 'ERROR:' . $e['message'] . ' in <b>' . $e['file'] . '</b> on line <b>' . $e['line'] . '</b><br/>';
                    $errMsg .= self::getFileInfo($e['file'], $e['line'], self::$lineOffset);
                    function_exists('halt') ? halt($e) : exit($errMsg);
                    break;
            }
        }
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
        if (self::$_debug) {
            $errorMsg = "filename:{$filename} in line {$linenum}<br/>" . $errmsg . "<br/>";
            $errorMsg .= self::getFileInfo($filename, $linenum - 3, $linenum + 3);
            die($errorMsg);
        }
    }

    static function getFileInfo($filename, $line, $offset)
    {
        ($startLine = $line - $offset - 1) < 0 && $startLine = 0;

        $len = $line + $offset - $startLine;

        $file = @file($filename);

        return implode('<br/>', array_slice($file, $startLine, $len));
    }
}