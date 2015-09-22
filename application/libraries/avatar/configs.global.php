<?php
	error_reporting(7);
	@header("Content-type: text/html; charset=utf-8");
	session_cache_limiter('public, must-revalidate');
	date_default_timezone_set('PRC');
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	/*
	|---------------------------------------------------------------
	| PHP ERROR REPORTING LEVEL
	|---------------------------------------------------------------
	*/
	//	error_reporting(E_ALL);
	
	define('UC_API', 'http://'.$_SERVER['HTTP_HOST'].'/avatar');
	define('UC_API_EXEC','http://'.$_SERVER['HTTP_HOST'].'/avatar'); //执行存储操作的文件
	define('UC_DATAURL', UC_API.'/data');
	define('UC_DATADIR', 'data'.DS);    //图片存放相对地址
    define('UC_KEY', 'hqNE7JHWjhLLIsKs');
?>