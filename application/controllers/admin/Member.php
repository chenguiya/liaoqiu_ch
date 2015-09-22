<?php

/* *
 *  会员管理模块
 * 
 * 
 */
require_once (__DIR__ . "/Base.php");
class Member extends Base
{
    
    public function __construct() 
    {
        parent::__construct();        
        load_model('member');
    }
    
    public function index($now_page = 1)
    {
        $params = array();
        $start_time = $this->input->get('start_time', true);
        $end_time = $this->input->get('end_time', true);
        $role = $this->input->get('role', true);
        $status = $this->input->get('status', true);
        $keyword = $this->input->get('keyword', true);
        $type = $this->input->get('type', true);
        if(!empty($start_time))
        {
            $params['start_time'] = strtotime($start_time);
        }
        
        if(!empty($end_time))
        {
            $params['end_time'] = strtotime($end_time);
        }
        
        if(!empty($role))
        {
            $params['role'] = intval($role);
        }
        
        if(isset($status) && ($status != ''))
        {
            $params['status'] = intval($status);
        }
        $this->data['keyword'] = '';
        if(!empty($keyword))
        {
            if($type == 2)
            {
                $params['member_id'] = trim($keyword);
            }
            else 
            {
                $params['account'] = trim($keyword);
            }
            $this->data['keyword'] = $keyword;
        }
        //总数
        $count = $this->liaoqiu_member_model->count_all($params);
        $add = 'admin/member/index/';
        //分页
        $page = $this->page_config($count, $now_page, $add);
        $params['start'] = ($now_page - 1)  * Liaoqiu_member_model::LIMIT;
        $list = $this->liaoqiu_member_model->getMemberList($params);
        if(!empty($list))
        {
            foreach ($list as &$row)
            {
                $row['role_msg'] = isset(Liaoqiu_member_model::$roles_arr[$row['role']]) ? Liaoqiu_member_model::$roles_arr[$row['role']] : Liaoqiu_member_model::ROLE_NORMAL;
                $row['status_msg'] = !empty($row['status']) ? '正常' : '冻结' ;
            }
        }
        
        $params['start_time']  = $start_time;
        $params['end_time'] = $end_time;
        $this->data['params'] = array_merge(array('start_time' => '', 'end_time' => '', 'role' => '', 'status' => '', 'type' => 2, 'keyword' => $this->data['keyword'] ), $params);
        
        $this->data['page'] = $page['page'];
        $this->data['member_list'] = $list;
        $this->show('member_list', $this->data);
        
    }
    /**
     * 设置角色（主播）
     * 
     */
    public function setrole()
    {
        $return = array('state_code' => 0, 'state_desc' => '设置成功');
        
        try
        {
            $role = $this->input->post('role', true);
            $id = $this->input->post('id', true);
            
            if(empty($role) || empty($id) || !array_key_exists($role, Liaoqiu_member_model::$roles_arr))
            {
                throw new Exception('参数错误', 21010);
            }
            
            $member = $this->liaoqiu_member_model->getMember($id);
            //$member = object2array($member);
            if(empty($member) )
            {
                throw new Exception('该用户不存在！', 21011);
            }
            if(($role == Liaoqiu_member_model::ROLE_ANCHOR) &&  ($member['role'] == Liaoqiu_member_model::ROLE_ANCHOR) )
            {
                throw new Exception('该用户已经是主播', 21012);
            }
            
            $result = $this->liaoqiu_member_model->editMemberByID($id, array('role' => $role));
            
            if(empty($result))
            {
                _E('保存失败', 21013);
            }
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
        //exit(json_encode($return));
    }
    
    /**
     * 冻结账号
     * 
     */
    public function changestate()
    {
        $return = array('state_code' => 0, 'state_desc' => '设置成功');
        
        try 
        {
            $status = $this->input->post('status', true);
            $id = $this->input->post('id', true);
            
            if(!isset($status) || empty($id) || !in_array($status, array(0, 1)))
            {
                throw new Exception('参数错误', 21010);
            }
            
            $id = is_array($id) ? $id : array($id);
            
            foreach ($id as $row)
            {

                $member = $this->liaoqiu_member_model->getMember($row);
                //$member = object2array($member);
                if(empty($member) )
                {
                    throw new Exception('该用户不存在！', 21011);
                }

                $result = $this->liaoqiu_member_model->editMemberByID($row, array('status' => $status));
            }
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
        //exit(json_encode($return));
        
    }
    /**
     * 添加聊球会员
     * 
     */
    public function add()
    {
        $return = array('state_code' => 0, 'state_desc' => '添加会员成功');
        
        try 
        {
            $email = $this->input->post('email', true);
            $passwd = $this->input->post('passwd', true);
            $account = $this->input->post('account', true);
            $role = $this->input->post('role', true);
            
            if(empty($email) || empty($passwd) || empty($account) || empty($role))
            {
                throw new Exception('参数错误', 21023);
            }
            
            if(!check_email($email))
            {
                _E("邮箱格式错误", 11001);
            }
            elseif (!is_password($passwd)) 
            {
                _E('密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素', 11002);
            }
            
            if(!array_key_exists($role, Liaoqiu_member_model::$roles_arr))
            {
                _E('角色错误', 11020);
            }
                        
            if(!check_nickname($account))
            {
                _E('昵称格式错误，2-20位字符，支持汉字、数字、字母', 11022);
            }
            
            $url =  'http://'  . $_SERVER["HTTP_HOST"] . '/register/registerbyemail/' . urlencode($email) . '/' . urlencode($passwd) . '/api?';
            $sign = urlencode($url) . $this->data['api_key'];
            $url .= 'sign=' . md5($sign);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            $ret = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            
            $result = json_decode($ret, true);
            if(!empty($err) || empty($result['token']))
            {
                $message = !empty($result['state_desc']) ? $result['state_desc'] : '添加会员失败';
                $code = !empty($result['state_code']) ? $result['state_code'] : 21015;
                throw new Exception($message, $code);
            }
            //注册成功
            //修改角色
            $member = getuser_by_token($result['token']);
            $this->liaoqiu_member_model->editMemberByMemberID($member['member_id'], array('role' => $role));
            //修改昵称
            if($result['user_detail']['nick_name'] != $account)
            {
                $account = check_account_exists($account);
                $url =  'http://'  . $_SERVER["HTTP_HOST"] . '/member/modify_info/' .   $member['token'] . '/NULL/' . urlencode($account).  '/NULL/NULL/NULL/NULL/NULL/NULL/api?';
                $sign = urlencode($url) . $this->data['api_key'];
                $url .= 'sign=' . md5($sign);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $ret = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                
                if(!empty($err))
                {
                    throw new Exception('修改会员昵称失败', 21045);
                }

            }
            
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
        //exit(json_encode($return));
        
    }
    

}