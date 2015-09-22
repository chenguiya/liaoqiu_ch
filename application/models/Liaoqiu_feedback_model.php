<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_feedback_model extends CI_Model {

    public function __construct()
    {
       $this->load->database();
    }

    // 修改反馈
    public function editByID($id, $data)
    {
       $this->db->where('id', $id);
       $this->db->update('feedback', $data);
    }

    /**
     *  插入一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
    		$userid = -1;
        // username email ucuserid要唯一
        if(trim($data['member_id']) != '')
        {
            $this->db->select('member_id, time');
            $this->db->from('feedback');
            $this->db->where(array('member_id' => trim($data['member_id'])));
            $row = $this->db->order_by('id desc')->limit(1,0)->get()->row_array();
            $old_userid = $row['member_id'] + 0;     // 已经有记录
            $nowtime = time();
            $logtime = $row['time'];
            $nntime = $nowtime - $logtime;
            //一天只能反馈一次
            if($old_userid > 0 && $nntime < 3600*1)
            {
                $userid = -2;
            }
            else
            {
                $this->db->insert('feedback', $data);
                $userid = $this->db->insert_id();
            }
        }
        return $userid;
    }

    /**
     * 获取一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('feedback');
        $member = $this->db->where('id', $id)->get()->row();
        return $member;
    }


}
