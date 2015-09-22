<?php error_reporting(0); if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * [活动报名Api] by liuzhengyong 更新：2015/5/28
 * ******************************************************************
 * [用户报名，参数：token,活动名称,sign]
 * /liaogeqiu/activity/apply/298374918a8f989281/activityid?sign=49f9240a78e1b2e7e2300c200f22f348
 *
 * [查询用户是否报名，参数：token,sign]
 * /liaogeqiu/activity/checkMember/298374918a8f989281?sign=49f9240a78e1b2e7e2300c200f22f348
 *
 */
 
class Activity extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "非法提交",
    );



    public function __construct(){
        parent::__construct();
		$this->load->model('member_model');
		$this->load->model('liaogeqiu/liaogeqiu_member_model');
		$this->load->model('liaogeqiu/liaogeqiu_activity_model');
		$this->load->model('liaogeqiu/liaogeqiu_activity_terry_model');
       	include_once("global.func.php");
    }

	//特里活动页面
	public function terryindex($token){
        $applyed = "N";
        $app_type = "1";
        if(@$_GET["app_type"]){
            $app_type = $_GET["app_type"];
        }
        if(strlen($token) == 32){
            $result = $this->_checkapply($token);
            if(!empty($result)){
                $applyed = "Y";
            }
        }
        $activity_info = array("name"=>"特里中国行", "applyed" => $applyed, "app_type" => $app_type);
		$this->load->view('liaogeqiu/activity_terry', $activity_info);
	}


	//插入用户报名信息
	public function apply($id, $token){
        verify_sign();
        $token = urldecode($token);
        $id = urldecode($id);
        //判断参数完整性
        if(empty($token) || empty($id)) {
            $res["state_code"] = -99;
            $res["state_desc"] = "参数错误";
            output($res);
        }
        $member = $this->_checkapply($token);
        if(!empty($member)){
            $res = array("state_code" => 1,"state_desc"=>"您已经报名！","status"=>1);
            output($res);
        }
        $member = $this->liaogeqiu_member_model->getMemberByToken($token);
        $data = array('member_id'=>$member->member_id,'activity_id'=>$id,'activity_time'=>time());
        $arr = $this->liaogeqiu_activity_terry_model->insert_member($data);
        $res = array("state_code" =>0,"state_desc"=>"报名成功！","status"=>1);
        output($res);
	}


	//查询用户是否报名
	public function checkapply($token){
        verify_sign();
        $token = urldecode($token);
        //判断参数完整性
        if(empty($token)) {
            $res["state_code"] = -99;
            $res["state_desc"] = "参数错误";
            output($res);
        }
        $result = $this->_checkapply($token);
        $res = array("state_code" =>0,"state_desc"=>"您已经报名！","status"=>1);
        output($res);
	}
	

	public function _checkapply($token){
        $member = $this->liaogeqiu_member_model->getMemberByToken($token);
        return $this->liaogeqiu_activity_terry_model->check_member_by_id($member->member_id);
	}

}
?>