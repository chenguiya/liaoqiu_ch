<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Base_model extends CI_Model {

	public function __construct()
    {
        $this->load->database();
    }

	//查询个数
	public function num($table,$where=array())
	{
		return $this->db->where($where)->count_all_results($table);
	}
	
	//查询多条数据
	public function select($table,$where='',$field='',$order='',$join='',$limit='')
	{
		if(!empty($where)) $where="where ".$where;
		if(!empty($order)) $order=$this->order($order);
		if(!empty($limit)) $limit="limit ".$limit;
		if(empty($field)) $field='*';
  		return $this->db->query ( "select $field from liaoqiu_$table as a ".$join.$where." $order $limit" )->result_array();
	}
	
	//查询多条数据
	public function row($table,$where='')
	{
		$this->db->select('*');
		if(!empty($where)) $this->db->where($where);
		return $this->db->get($table)->row_array();
	}	
	
	//查询单条数据
	public function rows($table,$where='',$field='',$join='')
	{
		if(!empty($where)) $where="where ".$where;
		if(empty($field)) $field='*';
  		return $this->db->query ( "select $field from liaoqiu_$table as a ".$join.$where)->row_array();
	}	
	
	//新增单条数据
	public function insert($table,$data)
	{
        $this->db->insert($table, $data);
        return $this->db->insert_id();
	}	
	
	//删除单条数据
	public function delete($table,$where)
	{
        return $this->db->delete($table,$where); 
	}
	
	//修改单条数据
    public function update($table,$where,$data)
    {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }				
	
	//自增1
    public function setinc($table,$where,$field,$num=1)
    {
		$sql = "update liaoqiu_$table set {$field}=({$field}+$num) where ".$where;
		return $this->db->query($sql);
    }
		
	//分页
    public function page($n,$o,$table,$where='',$join='',$order='',$field='')
    {
     	if($o==''){$o=1;}
		$o = ($o-1)*$n;
		if(empty($field)) $field='*';
		if(!empty($where)) $where="where ".$where;
		if(!empty($order)) $order=$this->order($order);
  		return $this->db->query ( "select $field from liaoqiu_$table as a ".$join.$where." $order limit $o,$n" )->result_array();
    }
		
	//分页
    public function _count($table,$where='',$join='')
    {
		if(!empty($where)) $where="where ".$where;
  		$result = $this->db->query ( "select count(*) as count from liaoqiu_$table ".$join.$where )->row_array();
		return $result['count'];
    }	
	//处理排序函数
	private function order($order='')
	{
		if(!empty($order)){
			$order_arr = explode(',', $order);
			$order = $order_arr[0];
			if(isset($order_arr[1])) $order .= " ".$order_arr[1];
			$order = "order by ".$order;
		}
		return 	$order;	
	}				


}
?>