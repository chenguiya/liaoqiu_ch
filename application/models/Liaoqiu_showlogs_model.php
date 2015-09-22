<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_showlogs_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
            $this->db->insert('showlogs', $data);
            $operate_id = $this->db->insert_id();
        return $operate_id;
    }

    public function getRecordByWhere($where)
    {
        $this->db->select('*');
        $this->db->from('showlogs');
        $record = $this->db->where($where)->get()->result_array();
        return $record;
    }

    public function getRecordsByShowId($id)
    {
        return $this->db->select('*')->where(array('show_id'=> $id))->get('showlogs')->result_array();
    }

    public function getRecordsByshowIDMemberID($id, $member_id)
    {
        return $this->db->select('*')->where(array('show_id'=> $id,'member_id'=>$member_id))->get('showlogs')->row_array();
    }

    public function getCountByshowIDMemberID($where)
    {
        return $this->db->where($where)->count_all_results('showlogs');
    }
    public function getNormalRecordsByGroupIDMemberID($id, $member_id)
    {
        $sql = "SELECT * FROM `usport_liaoqiu_showlogs` WHERE `group_id` = ".$id
        		." AND `member_id` = ".$member_id." AND `type` = '1'";
        $row = $this->db->query($sql)->result();
        return $row;
    }

    public function getRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('showlogs');
        $record = $this->db->where('member_id', $id)->get()->row_array();
        return $record;
    }

    public function getAvailableRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('showlogs');
        $record = $this->db->where(array('member_id' => $id, "type" => "1"))->get()->result();
        return $record;
    }


    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('showlogs');
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }
	
	public function page_list($n,$o,$id) {
     	if($o==''){$o=1;}
		$o = ($o-1)*$n;
  		return $this->db->query ( "select * from liaoqiu_showlogs where show_id=$id order by logs_time desc limit $o,$n" )->result_array();
 	}	
}
