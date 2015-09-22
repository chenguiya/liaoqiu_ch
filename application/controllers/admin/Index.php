<?php

include_once("Base.php");
class Index extends Base {
    public function __construct()
    {
        parent::__construct();
    }
		
	//后台首页
	public function index()
	{
            //账号信息
            load_model('admin');
            $username = $_COOKIE['username'];
            $data = array('username' => $username);
            $this->data['member'] = $this->liaoqiu_admin_model->getRecordByWhere($data);
            
            //所属角色
            if(!empty($this->data['member']['role']))
            {
                load_model('admin_role');
                $this->data['role'] = $this->liaoqiu_admin_role_model->get_one($this->data['member']['role']);
            }
            //print_r($this->data);exit;
            load_model('member');
            //总共注册用户人数    
            $params = array();
            $this->data['count'] = $this->liaoqiu_member_model->count_all($params);
            //总共注册主播人数
            $params = array(
                'role' => Liaoqiu_member_model::ROLE_ANCHOR,
            );
            $this->data['anchor_count'] = $this->liaoqiu_member_model->count_all($params);      
            //总共注册机器人人数
            $params = array(
              'role' => Liaoqiu_member_model::ROLE_NORMAL,  
            );
            $this->data['normal_count'] = $this->liaoqiu_member_model->count_all($params);
            //总共注册未禁用人数
            $params = array(
              'status' => Liaoqiu_member_model::STATUS_USABLE,  
            );
            $this->data['use_count'] = $this->liaoqiu_member_model->count_all($params);
            //总共注册禁用人数
            $params = array(
              'status' => Liaoqiu_member_model::STATUS_NOT_USABLE,  
            );
            $this->data['not_use_count'] = $this->liaoqiu_member_model->count_all($params);
            //总共一周内注册人数
            $params = array(
                'add_time>' => strtotime(strftime('%Y-%m-%d',strtotime('-1 week'))),
            );
            $this->data['day_count'] = $this->liaoqiu_member_model->count_all($params);
            //总共一月内注册人数
            $params = array(
                'add_time>' => strtotime(strftime('%Y-%m-%d',strtotime('-1 month'))),
            );
            $this->data['month_count'] = $this->liaoqiu_member_model->count_all($params);
            $this->show('index',$this->data);
	}
        
        public function update_admin()
        {
        
            $return = array(
                'state_code' => 0,
                'state_desc' => '请求成功'
            );
            try {
                
                //$this->load->library('session');
                $username = $_COOKIE['username'];
                
                if(empty($_POST) || empty($username))
                {
                    _E('参数不合法', 17000);
                }
                
                $data = array('username' => $username);
                $this->data['member'] = $this->liaoqiu_admin_model->getRecordByWhere($data);
                
                if(empty($this->data['member']))
                {
                    _E('用户不存在', 17001);
                }
                
                $name = $this->input->post('name', true);
                $password = $this->input->post('password', true);
                if(empty($name) && empty($password))
                {
                    _E('参数不合法', 17002);
                }
                
                $data = array();
                if(!empty($name) && ($name != $this->data['member']['name']) )
                {
                    $data['name'] = $name;
                }
                
                if(!empty($password))
                {
                    $password = md5($password);
                    if($password != $this->data['member']['password'])
                    {
                        $data['password'] = $password;
                    }
                }
                
                if(empty($data))
                {
                    _E('没有要修改的数据', 17003);
                }
                
                $result = $this->liaoqiu_admin_model->editRecordByID($this->data['member']['id'], $data);
                
                if(empty($result))
                {
                    _E('保存失败', 17004);
                }
            } catch (Exception $ex) {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            $this->format_print($return);
        }
}
