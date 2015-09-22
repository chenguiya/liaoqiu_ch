<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_group_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('group', $data);
    }

		public function deleteRecordByID($id) {
			$data=array();
			$data["status"] = "0";
			$this->db->where('id', $id);
      $this->db->update('group', $data);
		}
    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $operate_id = -1;
        $this->db->insert('group', $data);
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
        $this->db->from('group');
        $record = $this->db->where(array('id' => $id))->get()->row_array();
        return $record;
    }

    public function getRecordByManyID($arr_id, $status = false)
    {
        if(empty($arr_id))
        {
            return array();
        }
        
    	$where = implode(',', $arr_id);
        $sql = "select * from liaoqiu_group where id in (".$where." )";
        
        if($status !== FALSE)
        {
            $sql .= " AND `status` = " . $status;
        }
        
	$result = $this->db->query($sql)->result_array();
	
        return $result;
    }


    public function getAvalableRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array('id' => $id, "status"=> "1"))->get()->row_array();
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
        if(empty($id))
        {
            return array();
        }
        
        $sql = "select * from liaoqiu_group where id in (
        	select group_id from liaoqiu_group_members where member_id = ".$id."  ) and status = 1";
	    	$result = $this->db->query($sql)->result_array();
	    	return $result;
    }


    public function getRecordByArray($where)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where($where)->get()->row_array();
        return $record;
    }

    public function getRecordBySQL($sql)
    {
        $this->db->select('*');
        $this->db->from('group');
        $result = $this->db->query($sql)->result_array();
	    return $result;
    }


    public function getRecordsByUserID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array("member_id" => $id))->get()->result_array();
        return $record;
    }

    public function getAvalableRecordsByUserID($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $record = $this->db->where(array("member_id" => $id, "status" => "1"))->get()->result_array();
        return $record;
    }
}
