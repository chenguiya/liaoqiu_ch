<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Liaoqiu_photo_zan_model extends CI_Model {

    const TABLE_NAME = 'member_photo_zan';

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
        $sql = 'INSERT INTO liaoqiu_member_photo_zan(member_id,p_id,add_time,status) VALUES(%d,%d,%d,%d) ON DUPLICATE KEY UPDATE status=%d,add_time=%d';
        $sql = sprintf($sql, $data['member_id'], $data['p_id'], time(), $data['status'], $data['status'], time());
        $this->db->query($sql);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
    
    public function getRecordByPId($pid_arr, $member_id, $status = 1)
    {
        if(empty($pid_arr) || empty($member_id))
        {
            return FALSE;
        }
        
        $sql = 'select * from liaoqiu_member_photo_zan where  `p_id` IN (' . implode(',', $pid_arr) . ') AND `member_id` = ' . $member_id . ' AND status=1 ';
       
        $result = $this->db->query($sql);
        
        
        return $result->result_array();
    }
}
