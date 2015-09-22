<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_file_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('file', $data);
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
        if(trim($data['hx_uuid']) != '')
        {
            $this->db->select('id');
            $this->db->from('file');
            $this->db->where(array('hx_uuid'	=>	trim($data['hx_uuid'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//如果有记录，则返回-2
                $operate_id = -2;
            }
            else
            {
                $this->db->insert('file', $data);
                $operate_id = $this->db->insert_id();
            }
        }
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
        $this->db->from('file');
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordsByChatlogID($id)
    {
        $this->db->select('*');
        $this->db->from('file');
        $record = $this->db->where('chatlog_id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordsByGroupID($id)
    {
        $this->db->select('*');
        $this->db->from('file');
        $records = $this->db->where('group_id', $id)->get()->row_array();
        return $records;
    }

    public function getHotImagesRecordsByMatchID($id)
    {
        $sql = "select * from liaoqiu_file a, liaoqiu_chatlogs b,
    	 liaoqiu_group c where a.id = b.file_id and b.b_id = c.id and b.content_type = 2 and b.recommend = 1 and c.match_id=".$id;
    	$result = $this->db->query($sql)->result_array();
    	return $result;
    }

}
