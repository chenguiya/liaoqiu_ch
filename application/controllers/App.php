<?php error_reporting(0);if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	private $res = array(
		"state_code"	=>	-99,
		"state_desc"	=>	"参数错误！",
	);

	/**
	 * 构造函数
	 */
	public function __construct()
	{
	    parent::__construct();
        verify_sign();
	}

	//获取APP版本
	public function get_app_version($type) {
		//判断参数完整性
		if($type!='1' && $type!='2') output($this->res);
		//此处模拟输出版本信息
		$this->load->model('liaoqiu_app_version_model');
		$return = object_array($this->liaoqiu_app_version_model->getRecordByType($type));
		output($return);
	}

	//校验检测APP版本
	public function check_app_version($type, $version) {
		$version = urldecode($version);
		//判断参数完整性
		if(!isset($version) || ($type!='1' && $type!='2')) output($this->res);
		if(countstring($version)>10) {
			$this->res["state_desc"] = "version错误";
			output($this->res);
		}
		//此处模拟输出版本信息
		$this->load->model('liaoqiu_app_version_model');
		$return = object_array($this->liaoqiu_app_version_model->getRecordByType($type));
		if($return["version"] != $version) {
			$this->res["state_code"] = -1;
			$this->res["state_desc"] = "旧版本";
		} else {
			$this->res["state_code"] = 0;
			$this->res["state_desc"] = "已是最新版本";
		}
		$this->res["force_update"] = $return["force_update"];
		output($this->res);
	}

	//提交反馈信息
	public function feedback($token, $content,$contact_type, $version, $type, $manufactor, $device) {
		$manufactor = urldecode($manufactor);
		$device = urldecode($device);
		$version = urldecode($version);
		$content = urldecode($content);
		$manufactor = urldecode($manufactor);
		$device = urldecode($device);

		if(countstring($content)>200) {
			$this->res["state_desc"] = "content内容太长";
			output($this->res);
		}


		//校验token, 并顺便取用户基本信息
		if($token != 'NULL'){
			$result = getuser_by_token($token);
			$member_id = $result["member_id"];
		}else{
			$member_id = 0;
		}
		
		//写feedback表
		$feedback_param = array(
			"content"	=>	$content,
			"contact_type"	=>	$contact_type,
			"version"	=>	$version,
			"type"	=>	$type,
			"manufactor"	=>	$manufactor,
			"device"	=>	$device,
			"member_id"	=>	$member_id,
			"time"	=>	time(),
		);
		$this->load->model('liaoqiu_feedback_model');
		$return = $this->liaoqiu_feedback_model->setRecord($feedback_param);

		if($return > 0) {
			//此处模拟输出版本信息
			$this->res["state_code"] = 0;
			$this->res["state_desc"] = "记录成功";
		} else if($return == -2) {
			$this->res["state_code"] = -4;
			$this->res["state_desc"] = "你已提交过，1小时只能提交一次哦";
		} else {
			$this->res["state_code"] = -4;
			$this->res["state_desc"] = "服务器繁忙, 请稍候重试";
		}
		output($this->res);
	}

	//设置是否推送 1为推送,0为不推送
	public function switchpush($token, $push) {
		//判断参数完整性
		if(!isset($token) || !isset($push)) output($this->res);

		if($push!="1" && $push!="0") {
			$this->res["state_desc"] = "push错误";
			output($this->res);
		}

		//校验token, 并顺便取用户基本信息
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];

		$params = array("pushnews"=>$push);

		$this->load->model('liaoqiu_member_model');
		$this->liaoqiu_member_model->editMemberByMemberID($member_id, $params);

		//此处模拟输出版本信息
		$this->res["state_code"] = 0;
		$this->res["state_desc"] = "成功";
		output($this->res);
	}
	

	//提交反馈信息
	public function getpush($token) {
		if(!isset($token)) output($this->res);
		$result = getuser_by_token($token);
		$pushnews = array("pushnews"=>$result["pushnews"]);//获取推送状态
		$this->res["pushnews"] = $pushnews;
		$this->res["state_code"] = 0;
		$this->res["state_desc"] = "成功";
		output($this->res);
	}	
}

?>