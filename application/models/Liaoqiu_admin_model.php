<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_admin_model extends CI_Model {

    const LIMIT = 20;
    

    public function __construct()
    {
        $this->load->database();
    }
	
    public function setRecord($data)
    {
        $this->db->insert('admin', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
	
	// 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $operate_id = $this->db->update('admin', $data);
		 return $operate_id;
    }
	
    public function getRecordByWhere($data)
    {
      	return  $this->db->select('*')->where($data)->get('admin')->row_array();
    }		
	
    public function getList($params = array())
    {
        $where = $this->get_where($params);
        $start = !empty($params['start']) ? $params['start'] : 0;
        $limit = !empty($params['limit']) ? intval($params['limit']) : self::LIMIT;
        $table_name = 'liaoqiu_admin';
        
        $sql = sprintf('select * from %s where %s limit %d, %d', $table_name, $where, $start, $limit);
        return $this->db->query($sql)->result_array();
        
    }
    
    public function get_where($params = array())
    {
        $where = '1 ';
        
        if(!empty($params['lock']))
        {
            $where .= " AND `lock` = '{$params['lock']}' ";
        }
        return $where;
    }
    
        
    /**
     * 获取管理员详细
     * 
     * @param int $id
     */
    public function get_one($id)
    {
        $this->db->select('*');
        $this->db->from('admin');
        $role = $this->db->where('id', $id)->get()->row_array();
        return $role;
        
    }
    
    public function get_count($params = array())
    {
        $where = $this->get_where($params);
        $table_name = 'liaoqiu_admin';
        
        $sql = sprintf('select count(*) as c from %s where %s ', $table_name, $where);
        $return = $this->db->query($sql)->row_array();
        
        if(!empty($return['c']))
        {
            return $return['c'];
        }
        
        return 0;
    }
}
