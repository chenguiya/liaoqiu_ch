<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class liaoqiu_show_reserve_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
          return $this->db->update('show_reserve', $data);
    }
	
	
    // 修改
    public function editByShowId($show_id, $data)
    {
         $this->db->where('show_id', $show_id);
         return $this->db->update('show_reserve', $data);
    }


    // 修改
    public function editByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
          return $this->db->update('show_reserve', $data);
    }


    public function setRecord($data)
    {
        $this->db->insert('show_reserve', $data);
        return $this->db->insert_id();
    }

    public function getSumByshowID($id)
    {
         return $this->db->from('show_reserve')->where(array("show_id"=>$id,'status'=>1))->count_all_results();
    }



    public function getidByMemberID($id)
    {
         return $this->db->select('*')->where(array('member_id'=>$id,'status'=>1))->get('show_reserve')->row_array();
    }

    public function getRecordByshowIdUid($show_id,$uid)
    {
        return $this->db->from('show_reserve')->where(array("show_id"=>$show_id,"member_id"=>$uid,'status'=>1))->count_all_results();
    }

    public function getRecordByShowId($show_id)
    {
        return $this->db->select('*')->where(array('show_id'=>$id))->get('show_reserve')->row_array();
    }


}
