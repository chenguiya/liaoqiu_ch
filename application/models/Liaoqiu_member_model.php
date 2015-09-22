<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Liaoqiu_member_model extends CI_Model 
{
    /**
     * 聊球用户角色: 普通会员
     * 
     * @var  int 
     */
    const ROLE_NORMAL = 1;
    
    /**
     * 聊球用户角色: 主播
     * 
     * @var int
     */
    const ROLE_ANCHOR = 2;
    
    /**
     * 聊球用户角色: 机器人
     * 
     * @var  int
     */
    const ROLE_ROBOT = 3;
    
    /**
     * 场控
     * 
     * @var int
     */
    const ROLE_CONTROL = 4;
    public static $roles_arr = array(
        self::ROLE_NORMAL => '普通用户',
        self::ROLE_ANCHOR => '主播',
        self::ROLE_ROBOT => '机器人',
        self::ROLE_CONTROL => '场控',
    );
    
    /**
     * 会员状态：可用
     * 
     * @var int
     */
    const STATUS_USABLE = 1;
    
    /**
     * 会员状态：不可用
     * 
     * @var int
     */
    const STATUS_NOT_USABLE = 0;
    
    /**
     * TOKEN状态：可用
     * 
     * @var int
     */
    const TOKEN_STATUS_USABLE = 1;
    
    /**
     * TOKEN状态：不可用
     * 
     * @var int
     */
    const TOKEN_STATUS_NOT_USABLE = 0;
    
    /**
     * 推送消息:推送
     * 
     * @var  int
     */
    const PUSHNEWS_YES = 1;
    
    /**
     * 推送消息：不推送
     * 
     * @var int
     */
    const PUSHNEWS_NO = 0;
    
    /**
     * 分页
     * 
     */
    const LIMIT = 20;
    
    const TABLE_NAME = 'liaoqiu_member';
    
    public function __construct()
    {
        $this->load->database();
    }

    // 修改账号
    public function editMemberByID($id, $data)
    {
         $this->db->where('id', $id);
         $this->db->update('member', $data);
         
         return true;
    }

    public function updateMemberInfo($token, $arr_params) {
    	 if(count($arr_params)>0) {
    	 		 $update_feild = "";
	    	 	 foreach($arr_params as $k => $v) {
	    	 	 		//$email, $nickname, $sex, $address, $signiture
	    	 	 		if($k=="email") $update_feild .= ", b.email = '".$v."'";
	    	 	 		if($k=="nickname") $update_feild .= ", b.nickname = '".$v."'";
	    	 	 		if($k=="sex") $update_feild .= ", c.gender = ".$v;
	    	 	 		if($k=="address") $update_feild .= ", a.address = '".$v."'";
	    	 	 		if($k=="signiture") $update_feild .= ", c.introduction = '".$v."'";
	    	 	 }
	    	 	 $update_feild = substr($update_feild, 1, strlen($update_feild)-1);
					 $sql = "update liaoqiu_member a, liaoqiu_member b, liaoqiu_member_detail c set ".$update_feild." where a.token = '".$token."' and a.member_id = b.userid and a.member_id = c.userid;";
					 //echo $sql;
					 $this->db->query($sql);
			 }
    }

    // 修改账号
    public function editMemberByMemberID($id, $data)
    {
         $this->db->where('member_id', $id);
         $this->db->update('member', $data);
    }


    /**
     *  插入会员表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setMember($data)
    {
        $userid = -1;
        // username email ucuserid要唯一
        if(trim($data['member_id']) != '' && trim($data['account']) != '' && trim($data['hx_username']) != '')
        {
            $this->db->select('member_id');
            $this->db->from('member');
            $this->db->where(array('account' => trim($data['account']), 'hx_username' => trim($data['hx_username']), 'member_id' => trim($data['member_id'])));
            $row = $this->db->get()->row_array();
            $old_userid = $row['member_id'] + 0;     // 已经有记录
            if($old_userid > 0)
            {
                $userid = -2;
            }
            else
            {
                $this->db->insert('member', $data);
                $userid = $this->db->insert_id();
            }
        }
        return $userid;
    }

    /**
     * 获取会员表一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getMember($id)
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->where('id', $id)->get()->row_array();
        return $member;
    }

    public function getMemberByManyMemberID($id)
    {
        $sql = "select member_id,account,nick_name,hx_uuid,hx_username,status,member_logo from liaoqiu_member where member_id in (".$id.") and status = 1";
    	$result = $this->db->query($sql)->result_array();
    	return $result;
    }

    public function getMemberByMemberID($id)
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->where('member_id', $id)->get()->row_array();
        return $member;
    }

    public function getMemberByUsername($username)
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->where('account', $username)->get()->row();
        return $member;
    }

    public function getMemberByToken($token)
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->where('token', $token)->get()->row_array();
        return $member;
    }


    public function getMemberByUserID($userid) {
    	$sql = "select * from liaoqiu_member a, liaoqiu_member_detail b,
    	 liaoqiu_member c where a.userid = '".$userid."' and a.userid = b.userid and a.userid = c.member_id";
    	$result = $this->db->query($sql)->result();
    	return $result[0];
    }

    public function getMemberInfoByManyMemberID($userid) {
        if(empty($userid))
        {
            return array();
        }
        
        $sql = "select * from liaoqiu_member where member_id IN (".$userid.")";
    	$result = $this->db->query($sql)->result();
    	return $result;
    }



    
    //通过角色获取用户
    public function getMemberByRole($type){
        return $this->db->select('*')->where(array('role'=>$type))->get('member')->result_array();
    }

    /**
     * 写入聊球会员表
     * 
     */
    public function insertMember($result_5u_api, $huanxin_result, &$token)
    {
        if(empty($result_5u_api) || empty($huanxin_result))
        {
            return false;
        }
        
        $usport_userid = $result_5u_api['userid'];
        $time = time();
        $username = $result_5u_api["username"];
        $token = md5($result_5u_api['hx_username'].$time);
        $hx_uuid = $huanxin_result["huanxin_result"]["entities"][0]["uuid"];
        
        $liaogeqiu_result = $lq_reg_params = array();

        $lq_reg_params["member_id"] = $usport_userid;
        $lq_reg_params["account"] = $username;
        $lq_reg_params["nick_name"] = $result_5u_api['nick_name'];
        $lq_reg_params["hx_uuid"] = $hx_uuid;
        $lq_reg_params["hx_username"] = $result_5u_api['hx_username'];
        $lq_reg_params["hx_password"] = $result_5u_api['hx_userpass'];
        //$lq_reg_params["nickname"] = $username;
        $lq_reg_params["role"] = Liaoqiu_member_model::ROLE_NORMAL ;
        //$lq_reg_params["address"] = "";
        $lq_reg_params["token"] = $token;
        $lq_reg_params["token_status"] = Liaoqiu_member_model::TOKEN_STATUS_USABLE;
        $lq_reg_params["token_time"] = $time;
        $lq_reg_params["add_time"] = $time;
        $lq_reg_params["pushnews"] = Liaoqiu_member_model::PUSHNEWS_YES;
        $lq_reg_params["status"] = Liaoqiu_member_model::STATUS_USABLE ;
        
        $lq_reg_params['member_logo'] = $this->getDefaultMemberLogo();

        $liaogeqiu_result = $this->setMember($lq_reg_params);
        return $liaogeqiu_result;
    }
    
    /**
     * 默认的聊球角色
     * 
     * @return int
     */
    public function getDefaultRole()
    {
        return strval(self::ROLE_NORMAL);
    }
    
    public function getMemberList($params = array())
    {
        $where = $this->get_where($params);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : self::LIMIT;
        $orderby = isset($params['orderby']) ? $params['orderby'] : ' `add_time` DESC ';
        $table_name = self::TABLE_NAME;
        $sql = sprintf("select * from `%s` where %s ORDER BY %s LIMIT %d, %d ", $table_name, $where, $orderby, $start, $limit);
        return $this->db->query($sql)->result_array();
    }
    
    public function count_all($params = array())
    {
        $where = $this->get_where($params);
        $table_name = self::TABLE_NAME;
        $sql = sprintf("select count(*) as c from `%s` where %s", $table_name, $where);
        $return = $this->db->query($sql)->row_array();
        
        if(!empty($return['c']))
        {
            return $return['c'];
        }
        
        return 0;
    }

        /**
     * 组装SQL查询条件
     * 
     * @param array $params
     */
    public function get_where($params = array())
    {
        $where = "1 "; 
        if(!empty($params['member_id']))
        {
            $where .= " And `member_id` = '{$params['member_id']}' ";
        }
        
        if(isset($params['role']))
        {
            $where .= " And `role` = '{$params['role']}' ";
        }
        
        if(isset($params['status']))
        {
            $where .= " And `status` = '{$params['status']}' ";
        }
        
        if(!empty($params['account']))
        {
            $where .= " And `account` = '{$params['account']}' ";
        }
        
        if(!empty($params['start_time']) )
        {
            $where .= " And `add_time` >= '{$params['start_time']}' ";
        }    
            
        if(!empty($params['end_time']))
        {
            $where .= " And `add_time` <= '{$params['end_time']}' ";
        }    
        
        if(!empty($params['add_time>']))
        {
            $where .= " AND `add_time` >='{$params['add_time>']}' ";
        }
        
        if(!empty($params['members']) && is_array($params['members']))
        {
            $where .= " AND `member_id` IN ('" . implode("','", $params['members']) . "') ";
        }
        
        return $where;
    }
    
    
    
    public function getMemberBy5Uaccount($account) {
    	$sql = "select * from liaoqiu_member  where account = '".$account."' ";
    	$result = $this->db->query($sql)->row_array();
    	return $result;
    }

    public function getDefaultMemberLogo()
    {
        $default_member_logo = 'http://' . $_SERVER['HTTP_HOST'] . '/upload/head/data/avatar/noavatar_middle.jpg';
        return $default_member_logo;
    }
}
