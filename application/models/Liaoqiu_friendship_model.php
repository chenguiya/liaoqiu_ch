<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_friendship_model extends CI_Model {

    
    
    /**
     * 表名
     * 
     */
    const TABLE_NAME = 'friendship';
    
    
    /**
     * 关注状态：互相关注
     * 
     * @var int
     */
    const FOLLOW_EACH = 3;
    
    /**
     * 状态：关注我
     * 
     * @var int
     */
    const  FOLLOW_ME = 2;
        /**
     * 状态：我关注
     * 
     * @var int
     */
    const FOLLOW_NORMAL = 1;
    
    /**
     * 状态：不关注
     *
     * @var int
     */
    const FOLLOW_DELETE = 0;
    
    /**
     * 关注状态：关注
     * 
     * @var int
     */
    const STATUS_NORMAL = 1;
    
    /**
     * 关注状态：不关注
     *
     * @var int
     */
    const STATUS_DELETE = 0;
    
    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('friendship', $data);
    }

    // 根据用户ID，修改好友
    public function editRecordByMemberID($aid, $bid, $data)
    {
         $this->db->where(array('a_id'	=>	$aid, 'b_id' => $bid));
         $this->db->update('friendship', $data);
    }


    /**
     *  插入好友表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setRecord($data)
    {
        $operate_id = -1;
        // username email ucuserid要唯一
        if(trim($data['a_id']) != '' && trim($data['b_id']) != '')
        {
            $this->db->select('id');
            $this->db->from('friendship');
            $this->db->where(array('a_id'	=>	trim($data['a_id']), 'b_id' => trim($data['b_id'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//如果有记录，则直接更新状态
              $this->editRecordByMemberID(trim($data['a_id']), trim($data['b_id']), $data);
              $operate_id = $row['id'];
            }
            else
            {
                $this->db->insert('friendship', $data);
                $operate_id = $this->db->insert_id();
            }
        }
        return $operate_id;
    }

    /**
     * 获取好友表记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getRecordByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('friendship');
        $record = $this->db->where('a_id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordByAIDBID($aid, $bid)
    {
        $this->db->select('*');
        $this->db->from('friendship');
        $record = $this->db->where(array('a_id' => $aid, 'b_id' => $bid))->get()->row_array();
        return $record;
    }

    public function getRecordsByAID($aid)
    {
        $this->db->select('*');
        $this->db->from('friendship');
        $record = $this->db->where(array('a_id' => $aid, 'status'=>"1"))->get()->result_array();
        return $record;
    }


    public function getRecordsByBID($aid, $bid_arr, $status = 1)
    {
        $this->db->select('*');
        $this->db->from(self::TABLE_NAME);
        
        $record = $this->db;
        
        if(!empty($aid) && is_array($aid))
        {
            $record = $record->where_in('a_id', $aid);
        }
        else if(!empty($aid))
        {
            $record = $record->where('a_id', $aid);
        }
        
        if(!empty($bid_arr) && is_array($bid_arr))
        {
            $record = $record->where_in('b_id', $bid_arr);
        }
        elseif(!empty ($bid_arr))
        {
            $record = $record->where('b_id', $bid_arr);
        }
        
        $record = $record->where('status', $status);
        
        $record = $record->get()->result_array();
        
        return $record;
    }
    
    public function get_count($params = array())
    {
        $where = $this->get_where($params);
        $table_name = 'liaoqiu_' . self::TABLE_NAME;
        
        $sql = sprintf('select count(*) as c from `%s` where %s', $table_name, $where);
        $return = $this->db->query($sql)->row_array();
        
        if(!empty($return))
        {
            return $return['c'];
        }
        
        return 0;
    }
    
    public function get_where($params = array())
    {
        $where = " 1";
        
        if(!empty($params['a_id']))
        {
            $where .= " AND `a_id` = '{$params['a_id']}'";
        }
        
        if(!empty($params['b_id']))
        {
            $where .= " AND `b_id` = '{$params['b_id']}'";
        }
        
        if(isset($params['status']))
        {
            $where .= " AND `status` = '{$params['status']}'";
        }
        
        return $where;
    }

    // 修改记录
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         return $this->db->update(self::TABLE_NAME, $data);
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
}
