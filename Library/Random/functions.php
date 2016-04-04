<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午5:15
 */

/**
 * @param $url string 跳转地址
 * @param int $code 跳转代码
 * @author DJM <op87960@gmail.com>
 * @todo 重定向
 */
function redirect($url, $code = 302)
{
    header('location:' . $url, true, $code);
    exit;
}

function url()
{

}



