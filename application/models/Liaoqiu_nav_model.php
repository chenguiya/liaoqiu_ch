<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_nav_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
	
	/**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $this->db->insert('nav', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
	
	// 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('nav', $data);
		 return $id;
    }
	
    public function getRecord()
    {
        return $this->db->select('*')->order_by('id')->get('nav')->result_array();
    }
	
    public function getOKRecord()
    {
        return $this->db->select('*')->where('status',1)->get('nav')->result_array();
    }	
	
    public function getRecordById($id)
    {
       return $this->db->select('*')->where('id',$id)->get('nav')->row_array();
    }
	
    public function getRecordByHref($href)
    {
       return $this->db->select('*')->where('href',$href)->get('nav')->row_array();
    }	
    public function delRecordById($id)
    {
       return $this->db->delete('nav',array('id'=>$id));
    }		
}
