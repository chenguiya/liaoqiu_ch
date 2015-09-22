<?php

// echo 'http://zhangjh.dev.usport.cc/avatar/data/upload247.jpg';
// http://zhangjh.dev.usport.cc/avatar/index.php?m=user&inajax=1&a=uploadavatar&appid=1&input=null&agent=14787df35df2180fc53995160162b4c5&avatartype=virtual

/************************************************************
* FILE_NAME : index.php   
* FILE_PATH : D:\AppServ\www\ucflash\index.php
* 接受与处理图片上传，好像只用index接收。
*
* @copyright Copyright (c) 2009 - 2010 www.buynow.com.cn 
* @author BUYNOW项目组 deck
* 
* @version Mon Jul 05 17:20:25 CST 2010
**************************************************************/

require_once('configs.global.php');
require_once('avatar.php');

/**
 * 声明对象
 */
$objAvatar = new Avatar();
$content = var_export($_POST, TRUE);
// file_put_contents('log.txt',$content);
if ($objAvatar->getgpc('m') == 'user')
{
    //处理接收数据
    unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);

    $_GET = $objAvatar->daddslashes($_GET, 1, TRUE);
    $_POST = $objAvatar->daddslashes($_POST, 1, TRUE);
    $_COOKIE = $objAvatar->daddslashes($_COOKIE, 1, TRUE);
    $_SERVER = $objAvatar->daddslashes($_SERVER);
    $_FILES = $objAvatar->daddslashes($_FILES);
    $_REQUEST = $objAvatar->daddslashes($_REQUEST, 1, TRUE);
    
    //确定执行方法
    $a = $objAvatar->getgpc('a');
    $release = intval($objAvatar->getgpc('release'));
    $method = 'on'.$a;
    
    //开始保存操作
    if(method_exists($objAvatar, $method) && $a{0} != '_') {
        $data = $objAvatar->$method();
        echo is_array($data) ? $objAvatar->serialize($data, 1) : $data;
        exit;
    } elseif(method_exists($control, '_call')) {
        $data = $control->_call('on'.$a, '');
        echo is_array($data) ? $control->serialize($data, 1) : $data;
        exit;
    } else {
        exit('Action not found!');
    }
}
