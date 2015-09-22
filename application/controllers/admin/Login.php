<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
    }
	
	public function index(){
		if(empty($_POST)){
			$this->show('login');
		}else{
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$ip=ip();
			if(empty($username)) alert("用户名不能为空！",'',true);
			if(empty($password)) alert("密码不能为空！",'',true);
			$password=md5($password);
			$data = array('username'=>$username,'password'=>$password);
			$this->load->model('liaoqiu_admin_model');
			$member = $this->liaoqiu_admin_model->getRecordByWhere($data);
                        
			if(empty($member)) alert("账号或密码不正确，请联系管理员！",'',true);
                        $params = array(
                          'log_time' => time(),  
                        );
                        $this->liaoqiu_admin_model->editRecordByID($member['id'], $params);
                        //$this->load->library('session');
                        setcookie("username",$member['username'],time()+3600*3);
			$admin = $this->base_model->select('admin',"username='$username'","","","JOIN liaoqiu_admin_role ON liaoqiu_admin_role.id=a.role ");
			setcookie("role",$admin[0]['name'],time()+3600*3);
                        
                        
			alert('','show/showlist');
		}
	}
	

	public function out(){
		setcookie("username",'',time()-3600*3);	
		alert('退出成功','login');
	}

	private function show($action){
		$this->load->view('admin/'.$action);
	}
	

}
