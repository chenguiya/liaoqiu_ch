<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_show_model extends CI_Model {

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
        $this->db->insert('show', $data);
        $operate_id = $this->db->insert_id();
        return $operate_id;
    }
	
	// 修改记录
    public function editRecordByID($id, $data)
    {
         $this->db->where('id', $id);
         $operate_id = $this->db->update('show', $data);
		 return $operate_id;
    }
	
    public function getAvalableRecordByID($id)
    {
        $this->db->select('*');
        $this->db->from('show');
        $record = $this->db->where(array('id' => $id, "status"=> "1"))->get()->row_array();
        return $record;
    }
		
    public function getNotStartRecordByID($id)
    {
        return $this->db->select('*')->where(array('id' => $id, "status"=> "1","start_time >"=>time()))->get('show')->row_array();
    }	
		
    public function getAllAvalableRecord()
    {
        $this->db->select('*');
        $this->db->from('show');
        $record = $this->db->where(array("status"=> "1"))->get()->result_array();
        return $record;
    }
	
    public function getAllAvalableRecordByType($type)
    {
        $this->db->select('*');
        $this->db->from('show');
        $record = $this->db->where(array("status"=> "1","type"=>$type))->get()->result_array();
        return $record;
    }
	
		
    public function getAllAvalableRecordInOneMonth()
    {
        return $this->db->select('*')->where(array("status"=> "1",'start_time >'=>time()-3600*24*30))->get('show')->result_array();
    }
		
    public function getAllStartRecord()
    {
        return $this->db->select('*')->where(array("status"=> "1",'start_time <='=>time()+45*60,'end_time >='=>time()-15*60))->get('show')->result_array();
    }
	
	//通过比赛ID获取节目,当type为1时link_id指比赛ID，当type为2时 这里为话题类型
    public function getRecordByMatchId($matchid)
    {
        return $this->db->select('*')->where(array("status"=>"1",'type'=>1,'link_id'=>$matchid))->get('show')->row_array();
    }
	
	public function page_list($n,$o,$where,$join='') {
     	if($o==''){$o=1;}
		$o = ($o-1)*$n;
		if(empty($where)) $where='1=1';
  		return $this->db->query ( "select * from liaoqiu_show ".$join."where ".$where." order by start_time desc limit $o,$n" )->result_array();
 	}
	
	public function get_reserve_by_uid($member_id) {
		$where = 'member_id='.$member_id;
		$join = 'JOIN liaoqiu_show_reserve ON liaoqiu_show_reserve.show_id = a.id ';
  		return $this->db->query ( "select * from liaoqiu_show as a ".$join."where a.status=1 and ".$where." order by start_time desc")->result_array();
 	}
		
	public function get_list_by_time($where,$order,$limit){
		return $this->db->select('*')->where($where)->order_by('start_time',$order)->limit($limit)->get('show')->result_array();
 	}
	
	public function del_show_by_id($id)
	{
		return $this->db->delete('show',array('id' =>$id)); 
	}				
	
        public function getRecordByZhubo($zhubo = array())
        {
            return $this->db->select('*')->or_where_in('zhubo1', $zhubo)->or_where_in('zhubo2', $zhubo)->order_by('start_time', 'desc')->get('show')->result_array();
        }
        
        public function getRecordByLinkID($link_arr = array(), $type = 1)
        {
            
            $this->db->select('*');
            $params = array(
                'link_id' => $link_arr[0][0],
                'league_id' => $link_arr[0][1],
                'type' => $type
            );
            
            $this->db->where($params);
            foreach ($link_arr as $key => $row)
            {
                if(!empty($key))
                {
                    $params = array(
                        'link_id' => $row[0],
                        'league_id' => $row[1],
                        'type' => $type
                    );
            
                    $this->db->or_where($params);
                }
            }
            
            return $this->db->get('show')->result_array();
        }
        
        public function getRecordByIds($id_arr = array())
        {
            return $this->db->select('*')->where_in('id', $id_arr)->order_by('start_time', 'desc')->get('show')->result_array();
        }
}
