<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Liaoqiu_weibo_zan_model extends CI_Model {

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
        $this->db->insert('weibo_zan', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
	
	
    public function getRecord()
    {
        return $this->db->select('*')->where(array('w_status'=>1))->join('member', 'member.member_id = weibo_zan.member_id')->order_by('w_time','desc')->get('weibo_zan')->result_array();
    }

	
}
