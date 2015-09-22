<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

/*
 * 活动
 */
class Liaoqiu_activity_terry_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    //查询是否存在该用户 
    public function check_member_by_id($id){
         return $this->db->select('*')->where(array('member_id'=>$id))->get('activity_terry')->row_array();
    }
	
	//插入用户 
    public function insert_member($data){
         return $this->db->insert('activity_terry',$data);
    }



}
