<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Liaoqiu_weibo_data_model extends CI_Model {

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
        $this->db->insert('weibo_data', $data);
        return $this->db->insert_id();
    }
	
	// 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('w_id', $id);
         $operate_id = $this->db->update('weibo_data', $data);
		 return $operate_id;
    }	
	
	
    public function getRecord()
    {
        return $this->db->select('*')->where(array('w_status'=>1))->join('member', 'member.member_id = weibo_data.member_id')->order_by('w_time','desc')->get('weibo_data')->result_array();
    }
	
	//自增某个字段的数量	
    public function addNumByIdField($id,$field)
    {
		$sql = "update liaoqiu_weibo_data set {$field}=({$field}+1) where w_id=".$id;
		return $this->db->query($sql);
    }
	
	public function getRecordbyw_id($id)
    {
        return $this->db->select('*')->where(array('w_id'=>$id))->join('member', 'member.member_id = weibo_data.member_id')->get('weibo_data')->row_array();
    }
	
	public function getRecordbyw_id_uid($id,$uid)
    {
        return  $this->db->where(array('w_id'=>$id,'member_id'=>$uid))->count_all_results('weibo_data');
    }	
	
	public function getForward_pathbyw_id($id)
    {
       $r = $this->db->select('forward_path')->where('w_id',$id)->get('weibo_data')->row_array();
	    return $r['forward_path'];
    }	
	
	 public function page_list($n,$o) {
     	if($o==''){$o=1;}
		$o = ($o-1)*$n;
  		return $this->db->query ( "select * from liaoqiu_weibo_data as a JOIN liaoqiu_member ON liaoqiu_member.member_id = a.member_id where a.w_status=1 order by w_time desc limit $o,$n" )->result_array();
 	}	
	
}
