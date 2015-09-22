<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_grouplogs_temp_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

	// 插入表一条记录

    public function setRecord($data)
    {
        $operate_id = -1;
        if(trim($data['group_id']) != '')
        {
        	if(trim($data['num']) != '' || (trim($data['a_team_num']) != '' && trim($data['b_team_num']) != '')){
        		$data['create_time'] = time();
                $this->db->insert('grouplogs_temp', $data);
                $operate_id = $this->db->insert_id();
        	}
        }
        return $operate_id;
    }
	
	public function getRecordByGroupid($group_id)
    {
        return $this->db->select('*')->where(array('group_id'=>$group_id))->get('grouplogs_temp')->row_array();
    }

}
