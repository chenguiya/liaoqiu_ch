<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class liaoqiu_match_reserve_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('match_reserve', $data);
    }


    // 修改
    public function editByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
         $this->db->update('match_reserve', $data);
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
        if(trim($data['member_id']) != '' && trim($data['match_id']) != '' && trim($data['topic_id']) != '')
        {
            $this->db->select('id');
            $this->db->from('match_reserve');
            $this->db->where(array("member_id" => trim($data['member_id']), "match_id" => trim($data['match_id']), "topic_id" => trim($data['topic_id'])));
            $row = $this->db->get()->row_array();
            $old_id = @$row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//存在就更新
                $record_id = $this->editByID($old_id, $data);
            }
            else
            {
                $this->db->insert('match_reserve', $data);
                $record_id = $this->db->insert_id();
            }
        }
        return $record_id;
    }

    public function getSumByMatchID($id)
    {
        $this->db->select('*');
        $this->db->from('match_reserve');
        $record = $this->db->where('match_id', $id)->get()->result();
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
        $this->db->from('match_reserve');
        $record = $this->db->where('id', $id)->get()->row();
        return $record;
    }

    public function getByMemberID($id)
    {
        $sql = "select * from liaoqiu_match_reserve where member_id = ". $id . " and status = 1";
        $record = $this->db->query($sql)->result();
        return $record;
    }

    public function getRecordByMatchID($id)
    {
        $sql = "select * from liaoqiu_match_reserve where match_id = ". $id . " and status = 1";
        $record = $this->db->query($sql)->result();
        return $record;
    }

    public function getRecordByTopicID($id)
    {
        $sql = "select * from liaoqiu_match_reserve where reserve_type = 2 and topic_id = ". $id . " and status = 1";
        $record = $this->db->query($sql)->result();
        return $record;
    }


}
