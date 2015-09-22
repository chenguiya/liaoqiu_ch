<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Your Memcached servers can be specified below.
|
|	See: http://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/

$host_arr = array("www.5ulq.com","www.5usport.com","lq.5usport.com");
if(in_array($_SERVER["HTTP_HOST"],$host_arr)){  //外网
	$hostname = '192.168.11.7';
	$port = '11212';
} else {    //内网     
	$hostname = '172.17.33.33';
	$port = '11211';
}

$config = array(
	'default' => array(
		'hostname' => $hostname,
		'port'     => $port,
		'weight'   => '1',
	),
);
