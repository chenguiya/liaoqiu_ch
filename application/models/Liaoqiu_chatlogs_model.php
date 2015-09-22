<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_chatlogs_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('chatlogs', $data);
    }

    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $userid = -1;
        // a_id,b_id,要唯一
        if(trim($data['hx_msgid']) != '')
        {
            $this->db->select('id');
            $this->db->from('chatlogs');
            $this->db->where(array('hx_msgid' => trim($data['hx_msgid'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
                $userid = -2;
            }
            else
            {
                $this->db->insert('chatlogs', $data);
                $userid = $this->db->insert_id();
            }
        }
        return $userid;
    }

    /**
     * 获取表一条记录
     * @param int $id 用户id
     * @return $obj 聊天信息
     */
    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('chatlogs');
        $logs = $this->db->where('id', $id)->get()->row();
        return $logs;
    }

    /**
     * 根据用户ID获取表该用户的所有聊天记录
     * @param int $id 用户id
     * @return $obj 聊天信息
     */
    public function getRecordsByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('chatlogs');
        $logs = $this->db->where('a_id', $id)->get()->row();
        return $logs;
    }
	
	//通过接收者IDb_id，时间起始，时间结束获取记录
	public function getRecordsByBidStartTime($b_id,$start_time){
		$this->db->select('*')->where(array('b_id'=>$b_id));
		if(!empty($start_time) && $start_time !='NULL') $this->db->where(array('chattime <'=>$start_time));
        return $this->db->join('member','member.member_id=chatlogs.a_id')->order_by('chattime desc')->limit(20)->get('chatlogs')->result_array();
    }


}
