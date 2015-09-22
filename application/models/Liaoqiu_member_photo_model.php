<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_member_photo_model extends CI_Model 
{
    /**
     * 表名
     * 
     */
    const TABLE_NAME = 'member_photo';
    
    /**
     * 图片状态：正常
     * 
     * @var int
     */
    const STATUS_NORMAL = 0;
    
    /**
     * 图片状态：删除
     *
     * @var int
     */
    const STATUS_DELETE = 1;
    
    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         return $this->db->update(self::TABLE_NAME, $data);
    }

    /**
     *  插入表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        if(!isset($data['add_time']))
        {
            $data['add_time'] = time();
        }
        
        $this->db->insert(self::TABLE_NAME, $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }

    /**
     * 获取会员表一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getRecord($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordsByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        $record = $this->db->where('member_id', $id)->where('status', self::STATUS_NORMAL)->order_by('add_time desc')->get()->result_array();
        return $record;
    }
    

}
