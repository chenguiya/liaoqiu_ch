<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

/*
 * 活动
 */
class Liaoqiu_activity_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    //查询活动
    public function get_activity_by_id(){
         return $this->db->select('*')->where(array('status'=>1))->get('activity')->result_array();
    }
	


}
