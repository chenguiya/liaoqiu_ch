<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_jubao_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 修改记录
    public function editByID($id, $data)
    {
         $this->db->where('id', $id);
         return $this->db->update('jubao', $data);
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
        if(trim($data['member_id']) != '' && trim($data['chatlogs_uuid']) != '')
        {
            $this->db->select('id');
            $this->db->from('jubao');
            $this->db->where(array('member_id'	=>	trim($data['member_id']), 'chatlogs_uuid'	=>	trim($data['chatlogs_uuid'])));
            $row = $this->db->get()->row_array();
            $old_id = $row['id'] + 0;     // 已经有记录
            if($old_id > 0)
            {
            	//如果有记录，则返回-2
                $operate_id = -2;
            }
            else
            {
                $this->db->insert('jubao', $data);
                $operate_id = $this->db->insert_id();
            }
        }
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
        $this->db->from('jubao');
        $record = $this->db->where('id', $id)->get()->row_array();
        return $record;
    }

    public function getRecordsByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('jubao');
        $record = $this->db->where('member_id', $id)->get()->result_array();
        return $record;
    }

	
	public function page_list($n,$o,$where) {
     	if($o==''){$o=1;}
		$o = ($o-1)*$n;
		if(empty($where)) $where='1=1';
  		return $this->db->query ( "select * from liaoqiu_jubao where ".$where." order by jubao_time desc limit $o,$n" )->result_array();
 	}	

}
