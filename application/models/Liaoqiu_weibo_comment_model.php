<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Liaoqiu_weibo_comment_model extends CI_Model {

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
        $this->db->insert('weibo_comment', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
	
	
    public function getlistbyid($id)
    {
        return $this->db->select('*')->where(array('wc_status'=>1,'weibo_id'=>$id))->join('member', 'member.member_id = weibo_comment.member_id')->order_by('wc_time','desc')->get('weibo_comment')->result_array();
    }
	
	
}
