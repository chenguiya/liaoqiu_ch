<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class liaoqiu_chatlogs_timestamp_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('liaoqiu_chatlogs_timestamp', $data);
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
        if(trim($data['type']) != '' && trim($data['object_id']) != '')
        {
            $this->db->select('id');
            $this->db->from('liaoqiu_chatlogs_timestamp');
            $this->db->where(array('type' => trim($data['type']), 'object_id' => trim($data['object_id'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
                $userid = $this->editByID($old_id, $data);
            }
            else
            {
                $this->db->insert('liaoqiu_chatlogs_timestamp', $data);
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
        $this->db->from('liaoqiu_chatlogs_timestamp');
        $logs = $this->db->where('id', $id)->get()->row();
        return $logs;
    }

    public function getRecordByObjectIDType($object_id, $type)
    {
        $this->db->select('*');
        $this->db->from('liaoqiu_chatlogs_timestamp');
        $logs = $this->db->where(array('object_id' => $object_id, "type" => $type))->get()->row_array();
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
        $this->db->from('liaoqiu_chatlogs_timestamp');
        $logs = $this->db->where('a_id', $id)->get()->row();
        return $logs;
    }


}
