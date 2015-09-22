<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class liaoqiu_league_name_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
          return $this->db->update('league_name', $data);
    }

    public function setRecord($data)
    {
        $this->db->insert('league_name', $data);
        return $this->db->insert_id();
    }
	
    public function getRecord()
    {
        return $this->db->select('*')->where(array('status'=>1))->get('league_name')->result_array();
    }

}
