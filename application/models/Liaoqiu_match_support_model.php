<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_match_support_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('match_support', $data);
    }


    // 修改
    public function editByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
         $this->db->update('match_support', $data);
    }


    /**
     *  插入会员表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $record_id = -1;
        //一场比赛只能支持一次
        if(trim($data['member_id']) != '' && trim($data['match_id']) != '')
        {
            $this->db->select('id');
            $this->db->from('match_support');
            $this->db->where(array("member_id" => trim($data['member_id']), "match_id" => trim($data['match_id'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//存在就更新
                $record_id = $this->editByID($old_id, $data);
            }
            else
            {
                $this->db->insert('match_support', $data);
                $record_id = $this->db->insert_id();
            }
        }
        return $record_id;
    }

    public function getSumByMatchID($id)
    {
        $this->db->select('*');
        $this->db->from('match_support');
        $record = $this->db->where('match_id', $id)->get()->result_array();
        return $record;
    }

    /**
     * 获取会员表一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getByID($id)
    {
        $this->db->select('*');
        $this->db->from('match_support');
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }

    public function getByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('match_support');
        $record = $this->db->where('member_id', $id)->get()->row_array();
        return $record;
    }
	
	public function get_record_by_match_id_member_id($match_id,$member_id)
    {
        $this->db->select('*');
        $this->db->from('match_support');
        $record = $this->db->where(array('match_id'=>$match_id,'member_id'=>$member_id))->get()->row_array();
        return $record;
    }


}
