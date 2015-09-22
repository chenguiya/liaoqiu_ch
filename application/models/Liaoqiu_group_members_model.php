<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_group_members_model extends CI_Model {

    /**
     * 表名
     * 
     * @var string 表名称
     */
    const TABLE_NAME = 'group_members';

    public function __construct()
    {
        $this->load->database();
    }

    // 修改
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update(self::TABLE_NAME, $data);
    }

    public function editByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
         $this->db->update(self::TABLE_NAME, $data);
    }

    // 根据用户ID，修改
    public function editRecordByMemberAndGroupID($aid, $bid, $data)
    {
         $this->db->where(array('member_id'	=>	$aid, 'group_id' => $bid));
         $this->db->update(self::TABLE_NAME, $data);
    }

    public function deleteRecordByID($id) {

    }

    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $operate_id =  0;
        //要唯一
        if(trim($data['member_id']) != '' && trim($data['group_id']) != '')
        {
            $this->db->select('id');
            $this->db->from(self::TABLE_NAME);
            $this->db->insert(self::TABLE_NAME, $data);
            $operate_id = $this->db->insert_id();
        }
        return $operate_id;
    }

    /**
     * 获取记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getRecordByGroupID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where('group_id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordsByGroupID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where(array('group_id'=> $id, "type" => "1"))->get()->result_array();
        return $record;
    }

    public function getRecordsByGroupIDMemberID($id, $member_id)
    {
        $sql = "SELECT * FROM `liaoqiu_group_members` WHERE `group_id` = ".$id
        		." AND `member_id` = ".$member_id." AND (`type` = '0' OR `type` = '1' OR `type` = '4')";
        $row = $this->db->query($sql)->result_array();
        return $row;
    }

    public function getNormalRecordsByGroupIDMemberID($id, $member_id)
    {
        $sql = "SELECT * FROM `liaoqiu_group_members` WHERE `group_id` = ".$id
        		." AND `member_id` = ".$member_id." AND `type` = '1'";
        $row = $this->db->query($sql)->result_array();
        return $row;
    }

    public function getRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where('member_id', $id)->get()->row_array();
        return $record;
    }

    public function getAvailableRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where(array('member_id' => $id, "type" => "1"))->get()->result_array();
        return $record;
    }


    public function getRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }
    
        /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function insertRecord($data)
    {
        $operate_id =  0;
        //要唯一
        if(trim($data['member_id']) != '' && trim($data['group_id']) != '')
        {
            $sql = 'INSERT INTO liaoqiu_group_members(member_id,group_id,is_owner,logs_time,type) VALUES(%d,%d,%d,%d,%d) ON DUPLICATE KEY UPDATE type=%d,logs_time=%d';
            $sql = sprintf($sql, $data['member_id'], $data['group_id'], $data['is_owner'], time(), $data['type'], $data['type'], time());
            
            $this->db->query($sql);
            $operate_id = $this->db->insert_id();
        }
        return $operate_id;
    }
}
