<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_topic_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('topic', $data);
    }

    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $operate_id = -1;
        // username email ucuserid要唯一
        $this->db->insert('topic', $data);
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
        $this->db->from('topic');
        $record = $this->db->where('id', $id)->get()->row();
        return $record;
    }

    public function getRecordBySql($sql)
    {
        //$sql = "";
    	$result = $this->db->query($sql)->result();
    	return $result;
    }
	
	public function getRecordNotFinish()
	{
		return $this->db->select('*')->where(array('start_time <='=>time()+300,'end_time >'=>time()-300,'status'=>1))->get('topic')->result_array();
	}

}
