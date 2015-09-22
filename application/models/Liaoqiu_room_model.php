<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_room_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('room', $data);
    }

		public function deleteRecordByID($id) {
			$data=array();
			$data["status"] = "0";
			$this->db->where('id', $id);
      $this->db->update('room', $data);
		}
    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $this->db->insert('room', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }

    /**
     * 获取表一条记录
     * @param int $id 用户id
     * @return $obj 信息
     */
    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('room');
        $record = $this->db->where(array('id' => $id))->get()->row_array();
        return $record;
    }

    public function getAvalableRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('room');
        $record = $this->db->where(array('id' => $id, "status"=> "1"))->get()->row_array();
        return $record;
    }
	
    public function getAvalableRecord()
    {
        $this->db->select('*');
        $this->db->from('room');
        $record = $this->db->where(array("status"=> "1"))->get()->result_array();
        return $record;
    }	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

    public function getAvalableRecordByHXID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('hx_groupid' => $id, "status"=> "1"))->get()->row();
        return $record;
    }

    public function getAllAvalableRecordsByUserID($id)
    {
        //$sql = "select * from usport_group a, usport_grouplogs b
        //	where a.id = b.group_id and b.member_id = ".$id." and b.type = 1 and a.status = 1";
        $sql = "select * from usport_group where id in (
        	select group_id from usport_grouplogs where member_id = ".$id." and type = 1) and status = 1";
            //echo $sql;
	    	$result = $this->db->query($sql)->result();
	    	return $result;
    }


    public function getRecordByArray($where)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where($where)->get()->row();
        return $record;
    }

    public function getRecordBySQL($sql)
    {
        $this->db->select('*');
        $this->db->from('group');
        $result = $this->db->query($sql)->result();
	    return $result;
    }

    public function getRecordsByMatchID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where('match_id', $id)->get()->result();
        return $record;
    }
	

    public function getAvailableRecordsByMatchID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('match_id' => $id, "status"=>"1"))->get()->result();
        return $record;
    }

    public function getAvailableRecordsByTopicID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('topic_id' => $id, "group_type"=>"2", "status"=>"1"))->get()->result();
        return $record;
    }

    public function getAuthorGroupByMatchID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('match_id' => $id, "group_type" => "1", "is_author" => "1", "status" => "1"))->get()->result();
        return $record;
    }

    public function getAuthorGroupByTopicID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('topic_id' => $id, "group_type" => "2", "is_author" => "1", "status" => "1"))->get()->result();
        return $record;
    }

    public function getOwnerGroupByMatchIDMemberID($id, $userid)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('match_id' => $id, "userid" => $userid, "status" => "1"))->get()->result();
        return $record;
    }

    public function getRecordsByUserID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array("userid" => $id))->get()->result();
        return $record;
    }

    public function getAvalableRecordsByUserID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array("userid" => $id, "status" => "1"))->get()->result();
        return $record;
    }
}
