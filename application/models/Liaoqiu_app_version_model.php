<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_app_version_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改应用版本表
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('app_version', $data); 
    }

    /**
     * 获取版本表最新的一条记录
     * @param int $id 用户id
     * @return $obj 应用信息
     */
    public function getRecordByType($type)
    {
        $this->db->select('*');
        $this->db->from('app_version');
        //$record = $this->db->where(array('type'=> $type))->order_by('id desc')->limit(1,0)->get()->result();
        $record = $this->db->where(array('type'=> $type))->order_by('id desc')->limit(1,0)->get()->row();
        return $record;
    }
    
    public function getRecordsByType($type)
    {
        $this->db->select('*');
        $this->db->from('app_version');
        $record = $this->db->where(array('type'=> $type))->get()->result_array();
        return $record;
    }
    
    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('app_version');
        $record = $this->db->where(array('id'=> $id))->order_by('id desc')->limit(1,0)->get()->row();
        return $record;
    }

}
