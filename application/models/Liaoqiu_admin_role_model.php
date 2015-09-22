<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_admin_role_model extends CI_Model 
{
    /**
     * 表名
     * 
     */
    const TANEM_NAME = 'liaoqiu_admin_role';

    /**
     * 分页数
     * 
     */
    const LIMIT = 20;
    
    /**
     * 正常
     * 
     * @var int
     */
    const STATUS_NORMAL = 1;
            
    /**
     * 禁用
     * 
     * @var int
     */
    const STATUS_DELETE = 0;
    
    public static $status_arr = array(
        self::STATUS_NORMAL => '正常',
        self::STATUS_DELETE => '禁用'
    );
    
    
    public function __construct()
    {
        $this->load->database();
    }
    
    
    public function setRecord($data)
    {
        if(empty($data))
        {
            return FALSE;
        }
        
        if(!isset($data['add_time']))
        {
            $data['add_time'] = time();
        }
        
        $this->db->insert(self::TANEM_NAME, $data);
        $last_id = $this->db->insert_id();
        return $last_id;
    }

    // 修改记录
    public function editRecordByID($id, $data)
    {
        if(empty($id) || empty($data))
        {
            return FALSE;
        }
        
        if(isset($data['id']))
        {
            unset($data['id']);
        }
        
        $this->db->where('id', $id);
        
        $operate_id = $this->db->update(self::TANEM_NAME, $data);
	return $operate_id;
    }
    
    
    public function getRecordByWhere($data)
    {
      	return  $this->db->select('*')->where($data)->get(self::TANEM_NAME)->row_array();
    }
    
    public function get_where($params = array())
    {
        $where = "1 ";
        
        if(!empty($params['id']))
        {
            $where .= " AND `id` = '{$params['id']}' ";
        }
        
        if(isset($params['status']))
        {
            $where .= " AND `status` = '{$params['status']}' ";
        }
        else 
        {
            $where .= " AND `status` = 1 ";
        }
        if(!empty($params['add_time']))
        {
            $where .= " AND `add_time` = '{$params['add_time']}' ";
        }
        
        if(!empty($params['name']))
        {
            $where .= " AND `name` = '{$params['name']}' ";
        }
        
        return $where;
    }
    /**
     * 角色列表
     * 
     * @param array $params
     */
    public function get_roles($params = array())
    {
        $start = !empty($params['start']) ? intval($params['start']) : 0;
        $limit = !empty($params['limit']) ? intval($params['limit']) : self::LIMIT;
        $where = $this->get_where($params);
        $table_name = self::TANEM_NAME;
        $orderby = !empty($params['orderby']) ? $params['orderby'] : 'sort asc, id desc ';
        $sql = sprintf('select * from `%s` where %s order by %s limit %d, %d', $table_name, $where, $orderby, $start, $limit);
        
        return $this->db->query($sql)->result_array();
    }
    
    /**
     * 总数
     * 
     * @param array $params
     */
    public function get_count($params = array())
    {
        $where = $this->get_where($params);
        $table_name = self::TANEM_NAME;
        $sql = sprintf('select count(*) as c  from `%s` where %s', $table_name, $where);
        
        $return = $this->db->query($sql)->row_array();
        
        if(!empty($return))
        {
            return $return['c'];
        }
         
        return 0;
    }
    
    /**
     * 获取角色详细
     * 
     * @param int $id
     */
    public function get_one($id)
    {
        $this->db->select('*');
        $this->db->from('admin_role');
        $role = $this->db->where('id', $id)->get()->row_array();
        return $role;
        
    }
}
