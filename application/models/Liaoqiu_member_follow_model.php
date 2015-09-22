<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_member_follow_model extends CI_Model 
{
    /**
     * 表名
     * 
     */
    const TABLE_NAME = 'member_follow';
    

    
    public function __construct()
    {
        $this->load->database();
    }


}
