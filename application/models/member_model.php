<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

class Member_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
        $this->group_db = $this->load->database('group', TRUE);
    }

    // 修改账号
    public function editSsoMember($id, $data)
    {
         $this->db->where('uid', $id);
         $this->db->update('sso_members', $data);
    }

    // 修改账号
    public function editMember($id, $data)
    {
         $this->db->where('userid', $id);
         $this->db->update('member', $data);
    }

    /**
     *  插入SSO会员表一条记录
     * @param array $data 字段和值组成的二维数组
     * @return $int 成功返回会员ID，失败返回 -1 参数不足, -2 已经有账号
     */
    public function setSsoMember($data)
    {
        $uid = -1;
        // username email ucuserid要唯一
        if(trim($data['username']) != '' && trim($data['email']) != '' && trim($data['ucuserid']) != '')
        {
            $this->db->select('uid');
            $this->db->from('sso_members');
            $this->db->where('username =', trim($data['username']));
            $this->db->or_where('email =', trim($data['email']));
            $this->db->or_where('ucuserid =', trim($data['ucuserid']));
            $row = $this->db->get()->row_array();
            $old_uid = @$row['uid'] + 0;     // 已经有记录
            if($old_uid > 0)
            {
                $uid = -2;
            }
            else
            {
                $this->db->insert('sso_members', $data);
                $uid = $this->db->insert_id();
            }
        }
        return $uid;
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
        if(trim($data['username']) != '' && trim($data['email']) != '' && trim($data['phpssouid']) != '')
        {
            $this->db->select('userid');
            $this->db->from('member');
            $this->db->where('username =', trim($data['username']));
            $this->db->or_where('email =', trim($data['email']));
            $this->db->or_where('phpssouid =', trim($data['phpssouid']));
            $row = $this->db->get()->row_array();
            $old_userid = @$row['userid'] + 0;     // 已经有记录
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
     * 取会员表一条记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getMember($id)
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->where('userid', $id)->get()->row();
        return $member;
    }
	
	/**
     * 取所有会员表记录
     * @param int $id 用户id
     * @return $obj 会员信息
     */
    public function getAllMember()
    {
        $this->db->select('*');
        $this->db->from('member');
        $member = $this->db->get()->result();
        return $member;
    }

    /**
     * 取会员详细信息表一条记录
     * @param int $id 用户id
     * @return $obj 会员详细信息
     */
    public function getMemberDetail($id)
    {
        if($id + 0 == 0)
            return NULL;
            
        $member = $this->getMember($id+0);
        if(is_object($member))
        {
            $modelid = $member->modelid;
            $this->load->model('model_model');
            $model = $this->model_model->get($modelid);
			if(is_object($model))
			{
				$tablename = $model->tablename;
			}
			else
			{
				$tablename = '';
			}
            if($tablename != '')
            {
                $this->db->select('*');
                $this->db->from($tablename);
                $member = $this->db->where('userid', $id+0)->get()->row();
				if(is_object($member))
				{
					$_array = get_object_vars($member);
					$array = array();
					foreach($_array as $key => $value)
					{
						$value = (is_null($value)) ? '' : $value;
						$array[$key] = $value;
					}
					$member = (object) $array;
				}
                return $member;
            }
            else
            {
                return NULL;
            }
        }
        else
        {
            return NULL;
        }
    }

    /**
     * 取用户组表一条记录
     * @param int $id 会员组id
     * @return $obj 用户组信息
     */
    public function getMemberGroup($id)
    {
        $this->db->select('*');
        $this->db->from('member_group');
        $member = $this->db->where('groupid', $id)->get()->row();
        return $member;
    }

    /**
     * 取会员前台菜单表一条记录
     * @param int $id 识标id
     * @return $obj 会员前台菜单信息
     */
    public function getMemberMenu($id)
    {
        $this->db->select('*');
        $this->db->from('member_menu');
        $member = $this->db->where('id', $id)->get()->row();
        return $member;
    }

    /**
     * 取会员审核表一条记录
     * @param int $id 用户id
     * @return $obj 会员审核信息
     */
    public function getMemberVerify($id)
    {
        $this->db->select('*');
        $this->db->from('member_verify');
        $member = $this->db->where('userid', $id)->get()->row();
        return $member;
    }

    /**
     * 取会员VIP表一条记录
     * @param int $id 用户id
     * @return $obj 会员VIP信息
     */
    public function getMemberVip($id)
    {
        $this->db->select('*');
        $this->db->from('member_vip');
        $member = $this->db->where('userid', $id)->get()->row();
        return $member;
    }

    /**
     * 取SSO会员表一条记录
     * @param int $id 用户id
     * @return $obj SSO会员信息
     */
    public function getSsoMember($id)
    {
        $this->db->select('*');
        $this->db->from('sso_members');
        $member = $this->db->where('uid', $id)->get()->row();
        return $member;
    }

    /**
     * 取SSO会员表一条记录
     * @param int $id 用户账号
     * @return $obj SSO会员信息
     */
    public function getSsoMemberByUsername($username)
    {
        $this->db->select('*');
        $this->db->from('sso_members');
        $member = $this->db->where('username', $username)->get()->row();
        return $member;
    }


    /**
     * 根据条件取一组记录，支持相关表条件查询 暂不做缓存
     * @param varchar $data 查询字段
     * @param varchar $from 查询表
     * @param varchar $where 查询条件
     * @param int $pass 开始位置
     * @param int $number 数量
     * @param varchar $order 排序
     * @return obj $list 用户列表对象
     */
    public function getMemberList($data, $from, $where, $pass = 0, $num = 20, $order = '')
    {
        $this->db->select($data);
        $this->db->from($from);
        $this->db->where($where, NULL, FALSE);
        $list = $this->db->limit($num, $pass)->order_by($order)->get()->result_object();
        return $list;
    }

    //=================================下面是之前的，参考用=============================
    /**
     * 插入一条记录
     * @param array $arr_data 要插入字段和值组成的数组
     */
    public function set($arr_data)
    {
        // todo
    }

    /**
     * 更新一条记录
     * @param array $arr_data 要更新字段和值组成的数组
     * @param int $userid 用户ID
     */
    public function update($arr_data, $userid)
    {
        $this->db->where('userid', $userid);
        $this->db->update('member', $arr_data);
    }

    /**
     * 分页显示
     * @param array $arr_data   要分页的数据
     * @param int $now_page     当前页
     * @return string           要显示的HTML字符串
     */
    public function get_lists($arr_data, $now_page)
    {
        $int_count = count($arr_data) + 0;

        $html = "<style>";
        $html .= "page{font:12px/16px arial}";
        $html .= "page span{float:left;margin:0px 3px;}";
        $html .= " page a{float:left;margin:0 3px;border:1px solid #ddd;padding:3px 7px; text-decoration:none;color:#666}";
        $html .= "page a.now_page,#page a:hover{color:#fff;background:#05c}";
        $html .= "</style>";

        // 默认显示，例子详见page类
        $params = array(
            'total_rows' => $int_count,
            'now_page'   => $now_page,
            'list_rows'  => 20
        );
        $this->load->library('page', $params);

        // 输出页数
        $html .= '<div id="page">'.$this->page->show(1)."</div><br>";

        // 输出详细
        for($i = 0; $i < $int_count; $i++)
        {
            $int_start = $params['list_rows'] * ($params['now_page'] - 1);
            $int_end = $int_start + ($i % $params['list_rows']);

            if($int_start <= $i && $i <= $int_end)
            {
                $html .= $i."|".date('Y-m-d H:i:s', $arr_data[$i]['lastdate'])."|".$arr_data[$i]['username']."<br>";
            }
        }

        return $html;
    }

    /**
     * 取全部记录
     * @param array $data 查询条件数组
     * @return array 用户信息(二维数组)
     */
    public function get_rows($data = array())
    {
        $this->db->select('*');
        $this->db->from('member');
        if(count($data) > 0)
        {
            foreach($data as $key => $value)
            {
                $this->db->where($key, $value);
            }
        }
        $this->db->order_by("lastdate", "desc");
        $query = $this->db->get();
        $row = $query->result_array();
        return $row;
    }

    /**
     * 取一条记录
     * @param array $data 查询条件数组
     * @return array 用户信息(一维数组)
     */
    public function get($data)
    {
        if(count($data) > 0)
        {
            $this->db->select('*');
            $this->db->from('member');
            foreach($data as $key => $value)
            {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            $row = $query->result_array();

            if(count($row) >= 1){
                return $row[0];
			}else{
				return array();
			}
        }
        else
        {
            return array();
        }

    }

    public function getMemberLists($where = '', $limit = '', $order = '', $group = '', $key = '')  {
    	$this->db->select('userid,phpssouid,username,nickname,email,groupid,amount,point,modelid');
    	if (is_array($where)) $this->db->where($where);
    	if (!empty($limit)) $this->db->limit($limit);
    	if (!empty($order)) $this->db->order_by($order);
    	$result = $this->db->get('member')->result();
    	if (!empty($key)) {
	    	foreach ($result as $val) {
	    		$memberinfo[$val->$key] = $val;
	    	}
	    	return $memberinfo;
    	} else {
    		return $result;
    	}
    }

    public function getPositionMembers($groupid = array(), $modelid = 1, $limit = '', $recommend = 1, $order = '')  {
    	$members = array();
    	$MODELS = $this->getModels();
    	$extend_tablename = $MODELS[$modelid]['tablename'];
    	$this->db->select("member.userid,member.username,member.nickname,member.phpssouid,{$extend_tablename}.title");
    	$this->db->from('member');
    	$this->db->join($extend_tablename, "usport_member.userid = usport_$extend_tablename.userid", 'left');
    	$this->db->where_in('member.groupid', $groupid);
    	$this->db->where("usport_$extend_tablename.recommend", $recommend);
    	if (!empty($limit)) $this->db->limit($limit);
    	if (!empty($order)) $this->db->order_by($order);
    	$members = $this->db->get()->result();
    	foreach ($members as $key => $member) {
			$members[$key]->realname = getmemberrealname($member->userid, 1);
    		$members[$key]->avatar = getmemberucavatar($member->phpssouid, '', 'big');
    	}
    	return $members;
    }

    public function getModels($siteid = 1)  {
    	$result =  $this->db->where('siteid', $siteid)->group_by('modelid')->get('model')->result_array();
    	foreach ($result as $val) {
    		$models[$val['modelid']] = $val;
    	}
    	return $models;
    }

    public function getMemberByUsername($username = '', $data = '*')  {
    	$username = trim($username);
    	return $this->db->select($data)->where('username', $username)->get('member')->row();
    }

    public function getMemberByEmail($email)  {
    	$email = trim($email);
    	return $this->db->where('email', $email)->get('member')->row();
    }
    
    public function getMemberByNickName($nickname)  {
    	$nickname = trim($nickname);
    	return $this->db->where('nickname', $nickname)->get('member')->row();
    }

	public function getUseridByMobile($mobile)
	{
		$this->db->select('userid');
		$this->db->from('member_detail');
		$this->db->where(array('mobile_no' => trim($mobile)));
		$row = $this->db->get()->result_array();
		return $row;
	}
	
	// 登录时使用Mobile
	public function getMemberByMobile($data)
	{
		$extend_tablename = 'member';
		$this->db->select('member.*,member_detail.bind_mobile');
		$this->db->from('member_detail');
		if(@$data['all_search'] == 1)
		{
			$this->db->where(array('mobile_no' => $data['mobile']));
		}
		else
		{
			$this->db->where(array('mobile_no' => $data['mobile'], 'bind_mobile' => 1));
		}
		$this->db->join($extend_tablename, "member.userid = member_detail.userid", 'left');
		$row = $this->db->order_by('userid', 'asc')->get()->result_array();
		if(count($row) > 0)
		{
			return $row[0];
		}
		else
		{
			return array();
		}
	}
	
    public function getMemberByAgeGender($data)
    {
        $this->db->select('member_detail.userid, birthday, gender, YEAR(NOW()) - YEAR(birthday) as age');
        $this->db->from('member_detail');
        if(@$data['gender'] + 0 > 0)
        {
            $this->db->where(array('gender' => $data['gender'] + 0));
        }
        if(@$data['ageMin'] > 0)
        {
            $this->db->where(array('YEAR(NOW()) - YEAR(birthday) >=' => $data['ageMin']));
        }
        if(@$data['ageMax'] > 0)
        {
            $this->db->where(array('YEAR(NOW()) - YEAR(birthday) <=' => $data['ageMax']));
        }
        if(trim(@$data['userName']) != '')
        {
            $extend_tablename = 'member';
            $this->db->join($extend_tablename, "member.userid = member_detail.userid", 'left');
            $this->db->like('member.username', trim($data['userName']));
            $this->db->or_like('member.nickname', trim($data['userName'])); 
        }

        $row = $this->db->get()->result_array();
        // print_r($this->db->last_query());
        return $row;
    }

    public function getGroupMember($uid)  {
    	$uid = intval($uid);
    	$this->group_db->where('uid', $uid);
    	return $this->group_db->get('common_member_profile')->row_array();
    }

    //根据会员组获取会员数
    public function getNumMembers($where = '')  {
    	if (!empty($where)) {
			if (is_array($where)) $where = $this->toSql($where);
			$this->db->where($where);
		}
		return $this->db->count_all_results('member');
    }

    public function toSql($where, $font = ' AND ')  {
    	if (is_array($where)) {
    		$sql = '';
    		foreach ($where as $key=>$val) {
    			$sql .= $sql ? " $font `$key` = '$val' " : "$key` = '$val'";
    		}
    		return $sql;
    	} else {
    		return $where;
    	}
    }

     // 插入member_detail一条记录
    public function setMemberDetail($data)
    {
        $userid = -1;
        if(trim($data['userid']) != '')
        {
            $this->db->insert('member_detail', $data);
            $userid = $this->db->insert_id();
        }
        return $userid;
    }

    // 修改member_detail一条记录
    public function editMemberDetail($userid, $data)
    {
        $this->db->where('userid', $userid);
        $this->db->update('member_detail', $data);
    }

    // 记录Log日志
    public function setPassportLog($data)
    {
        $id = -1;
        if(trim($data['userid']) != '' && trim($data['content']) != '')
        {
            $this->db->insert('passport_log', $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    // 记录Log日志
    public function setPassportSmsLog($data)
    {
        $id = -1;
        $this->db->insert('passport_sms_log', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    
    // 取号码的发送日志
    public function getPassportSmsLog($data)
    {
        $this->db->select('id, posttime, mobile');
        $this->db->from('passport_sms_log');
        if($data['mobile'] + 0 > 0)
        {
            $this->db->where(array('mobile' => $data['mobile']));
        }
        $row = $this->db->limit(50, 0)->order_by('posttime', 'desc')->get()->result_array();
        return $row;
    }
	
	// 取passport用户token
	public function getPassportTokenTmp($data)
	{
		$this->db->select('*');
		$this->db->from('passport_token_tmp');
		foreach($data as $key => $value)
		{
			$this->db->where(array($key => $value));
		}
		$row = $this->db->limit(50, 0)->order_by('log_time', 'desc')->get()->result_array();
		return $row;
	}
	
	// 记passport用户token
	public function setPassportTokenTmp($data)
	{
		$this->db->insert('passport_token_tmp', $data);
		$id = $this->db->insert_id();
		return $id;
	}
	
	// 修改passport用户token
	public function editPassportTokenTmp($token, $data)
	{
		$this->db->where('token', $token);
		$this->db->update('passport_token_tmp', $data);
	}
}
