<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* *************************************************************************************
 * 群聊头像缩略图配置文件
 * 
 * 群聊头像尺寸是 100 * 100
 * 暂时只做3个图片的缩略图，每张图片尺寸 40 * 40
 * 这样
 * 第一张图片左上角x,y坐标是 x = 100/2 - 40/2  ; y = 10 ;
 ***************************************************************************************/
$config = array();

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

//第一张图片配置
$config['group_face'][0]['x'] = 30;
$config['group_face'][0]['y'] = 10;
$config['group_face'][0]['width'] = 40;
$config['group_face'][0]['height'] = 40;

//第二章图片配置
$config['group_face'][1]['x'] = 10;
$config['group_face'][1]['y'] = 50;
$config['group_face'][1]['width'] = 40;
$config['group_face'][1]['height'] = 40;

//第三张图片配置
$config['group_face'][2]['x'] = 50;
$config['group_face'][2]['y'] = 50;
$config['group_face'][2]['width'] = 40;
$config['group_face'][2]['height'] = 40;


//缩略图尺寸
$config['group_face']['thumb']['width'] = 40;
$config['group_face']['thumb']['height'] = 40;


//背景色
$config['group_face']['default_red'] = 239;
$config['group_face']['default_green'] = 239;
$config['group_face']['default_blue'] = 239;
$config['group_face']['default_alpha'] = 127;

//图片尺寸
$config['group_face']['width'] = 100;
$config['group_face']['height'] = 100;

//文件保存路径
$config['group_face']['save_path'] = APPPATH . '..' . DS . 'upload' .  DS . 'group';
?>