<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Base extends CI_Controller {
	    /**
     * 构造函数
     */
    
    public $data = array(
        'api_key'=>'33#$59*8<@',
    );

    
    public function __construct()
    {
        parent::__construct();
   	    if(!isset($_COOKIE['username']))
	    {
	        alert('尚未登录或者登录已超时，请重新登录！', 'login');
	    }
            
        /** 获取权限*/
        $this->get_power();
            
        $this->data['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin';
        $nav_arr = $this->_nav_list();
        $this->data['nav_list'] = $nav_arr['nav_list'];
        
        $this->data['parent_title'] = $nav_arr['parent_title'];  //父类导航
        $this->data['child_title'] = $nav_arr['child_title'];	//子类导航
		$this->data['sid'] = $this->getnavid('sid');//父类导航
        $this->data['cid'] = $this->getnavid('cid');//子类导航
        $this->data['jubao'] = $this->base_model->num('jubao',array('operate_status'=>0));
		$this->data['feedback'] = $this->base_model->num('feedback',array('status'=>0));
        $this->get_nav_by_power();    
    }


    /**
     * 加载视图文件
     * 
     * @param string $action
     * @param array $data
     */
    public function show($action,$data=array())
    {
        $this->load->view('admin/layout/default_head',$data);
		$this->load->view('admin/'.$action,$data);
		$this->load->view('admin/layout/default_footer');
    }
    
    /**
     * 分页
     * 
     * @param int $count 总数
     * @param string $add URL地址
     * @param  int $now_page 当前页
     * @param int $list_page
     */
    function page_config($count,$now_page,$url,$list_rows=20)
    {
         $this->load->helper('url');
		 $config = array(
	            'total_rows' => $count,
	            'method' => 'html',
		    	'parameter' =>base_url().$url.'?',
	            'now_page'=>$now_page,
	            'list_rows' =>$list_rows,
		 );	
		 $this->load->library('page',$config);
		 return array('page'=>$this->page->show(4),'list_page' =>$list_rows);
    }
	
	//导航列表、面包屑	
	function _nav_list()
	{
		$this->load->model('liaoqiu_nav_model');
		$list = $this->liaoqiu_nav_model->getOKRecord();
		$nav_list = array();
		$parent_title = array();
		$child_title  = array();
		$href = $this->get_url(); //根据url获取目录
		foreach ($list as $k => $v) {
			if($v['parentid']) $nav_list[$v['parentid']]['child'][] = $v;
				else $nav_list[$v['id']]['parent'] = $v;
		}
		$child_title = array('name'=>$href['name'],'href'=>$href['href'],'id'=>$href['id']);
		if(@$href['parentid']!=0){  //查找父类
			$parent_arr = $this->liaoqiu_nav_model->getRecordById($href['parentid']);
			$parent_title = array('name'=>$parent_arr['name'],'href'=>$parent_arr['href'],'id'=>$parent_arr['id']);
		} 		
		return array('nav_list'=>$nav_list,'parent_title'=>$parent_title,'child_title'=>$child_title);
	}
        
        /**
         * 格式化输出
         * 
         * @param mixed $data
         * @param string $type
         */
        public function format_print($data, $type = 'json')
        {
            switch ($type)
            {
                case 'json':
                    header("Content-type: application/json");
                    $data = json_encode($data);
                    echo $data;
                    break;
                default :
                    if(is_scalar($data))
                    {
                        echo $data;
                    }
                    break;
            }
            exit;
        }
        
        	
		//获取url，主要用于面包屑
        public function get_url()
        {
        	$arr = explode('?',@$_SERVER['REQUEST_URI']);
			$arr = explode('/',$arr[0]);
			$href = '';
			unset($arr[0]); //删除第一个空值
			foreach ($arr as $k => $v) {
				if($k<4) $href .= '/'.$v;  //只取签名三个
			}
			$arr = $this->liaoqiu_nav_model->getRecordByHref($href);
			return $arr;
        }        
        

        /**
         * 获取用户的权限信息
         * 
         */
        public function get_power()
        {
            $username = $_COOKIE['username'];
            
            $params = array('username' => $username);
            load_model('admin');
            $manager = $this->liaoqiu_admin_model->getRecordByWhere($params);
            load_model('admin_role');
            $role_power = $this->liaoqiu_admin_role_model->get_one($manager['role']);
            
            if(empty($role_power))
            {
                echo '角色不存在！';
                header('Location:/admin/login');
                exit ;
            }
            
            $this->data['user_power'] = explode(',', $role_power['manager']);
        }
        
        public function get_nav_by_power()
        {
            $owner_power = array();
            if(!empty($this->data['user_power']))
            {
                foreach ($this->data['nav_list'] as $i => $nav)
                {
                    if(!in_array($nav['parent']['id'], $this->data['user_power']))
                    {
                        continue; 
                    }
                    
                    foreach ($nav['child'] as $key => $val)
                    {
                        if(!in_array($val['id'], $this->data['user_power']))
                        {
                            unset($nav['child'][$key]);
                        }
                    }
                    
                    $owner_power[$i] = $nav; 
                }
            }
            
            $this->data['nav_list'] = !empty($owner_power) ? $owner_power : $this->data['nav_list'];
        }
		
		public function getnavid($type)
		{
	        if(isset($_GET[$type])){
	        	setcookie($type,$_GET[$type],0,3600);
				$id = $_GET[$type];
	        }else if(isset($_COOKIE[$type])){
	        	$id = $_COOKIE[$type];
	        }else{
	        	$id = 0;
	        }
			return $id;
		}
}