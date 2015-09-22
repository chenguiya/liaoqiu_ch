<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_blacklist_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('blacklist', $data);
    }

    // 根据用户ID，修改黑名单
    public function editRecordByMemberID($aid, $bid, $data)
    {
         $this->db->where(array('a_id'	=>	$aid, 'b_id' => $bid));
         $this->db->update('blacklist', $data);
    }


    /**
     *  插入黑名单表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $operate_id = -1;
        // username email ucuserid要唯一
        if(trim($data['a_id']) != '' && trim($data['b_id']) != '')
        {
            $this->db->select('id');
            $this->db->from('blacklist');
            $this->db->where(array('a_id'	=>	trim($data['a_id']), 'b_id' => trim($data['b_id'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//如果有记录，则直接更新状态
              $this->editRecordByMemberID(trim($data['a_id']), trim($data['b_id']), $data);
              $operate_id = $row['id'];
            }
            else
            {
                $this->db->insert('blacklist', $data);
                $operate_id = $this->db->insert_id();
            }
        }
        return $operate_id;
    }

    /**
     * 获取黑名单表记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('blacklist');
        $record = $this->db->where('a_id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordByAIDBID($aid, $bid)
    {
        $this->db->select('*');
        $this->db->from('blacklist');
        $record = $this->db->where(array('a_id' => $aid, 'b_id' => $bid))->get()->row_array();
        return $record;
    }

    public function getRecordsByAID($aid)
    {
        $this->db->select('*');
        $this->db->from('blacklist');
        $record = $this->db->where(array('a_id' => $aid))->get()->result_array();
        return $record;
    }

    public function getStatus1RecordsByAID($aid)
    {
        $this->db->select('*');
        $this->db->from('blacklist');
        $record = $this->db->where(array('a_id' => $aid, 'status'=>'1'))->get()->result();
        return $record;
    }

}
