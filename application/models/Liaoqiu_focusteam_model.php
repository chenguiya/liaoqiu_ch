<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_focusteam_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('focusteam', $data); 
    }
    
    
    // 修改
    public function editByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
         $this->db->update('focusteam', $data);
    }


    /**
     *  插入会员表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {

          $this->db->insert('focusteam', $data);
          return $this->db->insert_id();
    }

    /**
     * 获取会员表一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getByID($id)
    {
        $this->db->select('*');
        $this->db->from('focusteam');
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }
    
    public function getByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('focusteam');
        $record = $this->db->where('member_id', $id)->get()->row_array();
        return $record;
    }
    

}
