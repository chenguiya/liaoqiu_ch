<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 登录 退出 账号中心
 *
 * @author xiaoquanji
 */
class Login extends CI_Controller
{
    private $res = array(
        "state_code" => -99,
        "state_desc" => "非法提交",
    );
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('liaoqiu_member_model');
        $this->load->helper('huanxin_helper');
        verify_sign();
    }
    
    
    //登录
    public function login($username, $password) 
    {
        $username = urldecode($username);
        $password = urldecode($password);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        
        try
        {
            if(empty($username) || empty($password))
            {
                _E('参数错误', -99);
            }
            //step1 数据校验
            if(!is_username($username))
            {
                _E('账号格式错误，手机号/邮箱/用户名', 12010);
            }
            else if(countstring($password)<6 || countstring($password)>20) 
            {
                _E('密码格式错误；格式为：6-20位，且必须包含（字母，数字，符号）中至少两种元素', 12011);
            }
            
            
            
            //step2: 通过接口，让5u先校验账号密码是否正确，并且接口会返回用户的一些令牌。
            $params = array();
            $params["username"] = $username;
            $params["password"] = $password;
            $result_5u_api = json_decode(usport_api("member_login", $params), true);

            if($result_5u_api["state_code"]!="0" || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" || $result_5u_api["member_logo"]=="") {
                $this->res["state_code"] = $result_5u_api["state_code"];
                $this->res["state_desc"] = "5u接口返回：".$result_5u_api["state_desc"];
                output($this->res);
            }

            //读取用户表
            $return_lq_member = $this->liaoqiu_member_model->getMemberByMemberID($result_5u_api["userid"]);
            //$return_lq_member = object_array($member);
            //修改最后登录时间
            $this->liaoqiu_member_model->editMemberByID($return_lq_member['id'], array('token_time' => time()));
            
            //echo "<pre>"; print_R($return_lq_member);
            if(count($return_lq_member)==0) 
            {
                //如果5U会员中心存在此会员，但聊球这边不存在，则需要注册一个账号
                //step3: 去环信注册
                $bool_hx_reg_result = true;
                $result_5u_api['hx_username'] = $result_5u_api['hx_userpass'] = '';
                $usport_userid = $result_5u_api['userid'];
                $huanxin_result = batchregister_huanxin($result_5u_api, $bool_hx_reg_result,  $result_5u_api['hx_username'], $result_5u_api['hx_userpass']);
                //echo "<pre>"; print_r($huanxin_result);echo "</pre>";

                if(!$bool_hx_reg_result) 
                {
                    _E('服务器繁忙，请稍候重试1', -4);
                }

                //step4: 环信注册成功, 写聊球会员表, 取token令牌，返回给客户端
                $token = '';
                $liaogeqiu_result = $this->liaoqiu_member_model->insertMember($result_5u_api, $huanxin_result, $token);

                unset($result_5u_api["state_code"], $result_5u_api["state_desc"], $result_5u_api["username"], $result_5u_api["userid"], $result_5u_api['hx_username'] , $result_5u_api['hx_userpass']);
                //unset($lq_reg_params["hx_password"]);
                $lq_reg_params = array_merge($result_5u_api, $lq_reg_params);
                
                $lq_reg_params['fans_count'] = get_fans_count($usport_userid);
                $lq_reg_params['follows_count'] = get_follows_count($usport_userid);
                $lq_reg_params['focusteam'] = get_focusteam($usport_userid);
                $lq_reg_params['album'] = get_album($usport_userid);
                $lq_reg_params['qcloud_sig'] = qcloud_sig($usport_userid);
                
                
                //此处模拟成功的信息
                $result["state_code"] = 0;
                $result["state_desc"] = "登录成功";
                $result["token"] = $token;
                $result["user_detail"] = $lq_reg_params;
                //echo "<pre>";print_R($this->res);
                output($this->res);
            } else {
                $usport_userid = $return_lq_member["member_id"];
                $username = $return_lq_member["account"];
                $hx_uuid = $return_lq_member["hx_uuid"];
                $hx_username = $return_lq_member["hx_username"];
                $hx_userpass = $return_lq_member["hx_password"];
                //$hx_nickname = $return_lq_member["account"];
                $role = $return_lq_member["role"];
                //$address = $return_lq_member["address"];
                $token = $return_lq_member["token"];
                $token_status = $return_lq_member["token_status"];
                $token_time = $return_lq_member["token_time"];
                $add_time = $return_lq_member["add_time"];
                $pushnews = $return_lq_member["pushnews"];
                $status = $return_lq_member["status"];
                
                
                //此处模拟成功的信息
                $lq_reg_params = array();
                unset($result_5u_api["state_code"], $result_5u_api["state_desc"], $result_5u_api["username"], $result_5u_api["userid"]);
                
                
                $lq_reg_params = $result_5u_api;
                $lq_reg_params["member_id"] = $usport_userid;
                $lq_reg_params["account"] = $username;
                $lq_reg_params["hx_uuid"] = $hx_uuid;
                $lq_reg_params["hx_username"] = $hx_username;
                $lq_reg_params["hx_password"] = $hx_userpass;
                $lq_reg_params["role"] = $role;
                //$lq_reg_params["address"] = $address;
                $lq_reg_params["token"] = $token;
                $lq_reg_params["token_status"] = $token_status;
                $lq_reg_params["token_time"] = $token_time;
                $lq_reg_params["add_time"] = $add_time;
                $lq_reg_params["pushnews"] = $pushnews;
                $lq_reg_params["status"] = $status;
                $lq_reg_params['fans_count'] = get_fans_count($usport_userid);
                $lq_reg_params['follows_count'] = get_follows_count($usport_userid);
                $lq_reg_params['focusteam'] = get_focusteam($usport_userid);
                $lq_reg_params['album'] = get_album($usport_userid);
                $lq_reg_params['qcloud_sig'] = qcloud_sig($usport_userid);
                $lq_reg_params['member_logo'] = $return_lq_member['member_logo'];
                
                $result["state_code"] = 0;
                $result["state_desc"] = "登录成功";
                $result["token"] = $token;
                
                $result["user_detail"] = $lq_reg_params;
            }
           
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }
    
    
    
    //第三方平台登录接口
    public function loginbyoauth($oauth_type, $oauth_id) {
        $oauth_type = urldecode($oauth_type);
        $oauth_id = urldecode($oauth_id);
        //判断参数完整性
        if(!isset($oauth_type) || !isset($oauth_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $arr_oauth_type = array(
            "1" => "qq",
            "2" => "sina",
            "3" => "weixin",
        );

        if(!array_key_exists($oauth_type, $arr_oauth_type)){
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "第三方类型错误";
            output($this->res);
        }

             
        $params = array();
	$params["from"] = $arr_oauth_type[$oauth_type];
        $params["connectid"] = $oauth_id;


		//step2: 通过接口，获取赛程。
		$result_5u_api = json_decode(usport_api("login_oauth2", $params), true);
        if($result_5u_api["state_code"]!="0" || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" ) {
            output($result_5u_api);
        }
        
        //用户头像
        $member_logo = $this->input->post('member_logo', true);
        $member_logo = $this->get_member_logo($member_logo, $result_5u_api["userid"]);
        
        //读取用户表
        $return_lq_member = $this->liaoqiu_member_model->getMemberByMemberID($result_5u_api["userid"]);
        //$return_lq_member = object_array($member);
        //echo "<pre>"; print_R($return_lq_member);
        if(count($return_lq_member)==0) {
            //如果5U会员中心存在此会员，但聊球这边不存在，则需要注册一个账号
            //step3: 去环信注册
            $usport_userid = $result_5u_api["userid"];
            $hx_username = "5usport_".$usport_userid;
            $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);
            $member_logo = !empty($member_logo) ? $member_logo : $result_5u_api["member_logo"];
            $username = $result_5u_api["username"];

            $hx_action = "batchregister";
            $liboparams = array("user" => $hx_username, "pass"=> $hx_userpass);
            $huanxin_result = huanxin($hx_action, $liboparams);

            $bool_hx_reg_result = true;
            if($huanxin_result["code"]!="0" || !array_key_exists("entities", $huanxin_result["huanxin_result"])) {
                $bool_hx_reg_result = false;
            } else if(count($huanxin_result["huanxin_result"]["entities"])!=1) {
                $bool_hx_reg_result = false;
            } else if($huanxin_result["huanxin_result"]["entities"][0]["uuid"]=="") {
                $bool_hx_reg_result = false;
            }

            //echo "<pre>"; print_r($huanxin_result);echo "</pre>";

            if(!$bool_hx_reg_result) {
                $this->res["state_code"] = -4;
                $this->res["state_desc"] = "服务器繁忙，请稍候重试1";
                output($this->res);
            }

            //step4: 环信注册成功, 写聊球会员表, 取token令牌，返回给客户端
            $time = time();
            $token = md5($hx_username.$time);
            $hx_uuid = $huanxin_result["huanxin_result"]["entities"][0]["uuid"];
            $liaogeqiu_result = array();


            $lq_reg_params = array();
            $lq_reg_params["member_id"] = $usport_userid;
            $lq_reg_params["account"] = $username;
            $lq_reg_params["hx_uuid"] = $hx_uuid;
            $lq_reg_params["hx_username"] = $hx_username;
            $lq_reg_params["hx_password"] = $hx_userpass;
            //$lq_reg_params["nickname"] = $username;
            $lq_reg_params["role"] = "1";
            //$lq_reg_params["address"] = "";
            $lq_reg_params["token"] = $token;
            $lq_reg_params["token_status"] = "1";
            $lq_reg_params["token_time"] = $time;
            $lq_reg_params["add_time"] = $time;
            $lq_reg_params["pushnews"] = "1";
            $lq_reg_params["status"] = "1";
            $lq_reg_params['nick_name'] = $result_5u_api['nick_name'];
            
            $lq_reg_params['member_logo'] = !empty($member_logo) ? $member_logo : $this->liaoqiu_member_model->getDefaultMemberLogo();

            $liaogeqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);

            unset($result_5u_api["userid"], $result_5u_api["username"], $result_5u_api["state_desc"], $result_5u_api["state_code"], $result_5u_api['member_logo']);
            //unset($lq_reg_params["hx_password"]);
            $lq_reg_params = array_merge($lq_reg_params, $result_5u_api);

            $lq_reg_params['fans_count'] = get_fans_count($usport_userid);
            $lq_reg_params['follows_count'] = get_follows_count($usport_userid);
            $lq_reg_params['focusteam'] = get_focusteam($usport_userid);
            $lq_reg_params['album'] = get_album($usport_userid);
            $lq_reg_params['qcloud_sig'] = qcloud_sig($usport_userid);
                
            //此处模拟成功的信息
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "登录成功";
            $this->res["token"] = $token;
            $this->res["user_detail"] = $lq_reg_params;
            //echo "<pre>";print_R($this->res);
            output($this->res);
        } else {
            $usport_userid = $return_lq_member["member_id"];
            $username = $return_lq_member["account"];
            $hx_uuid = $return_lq_member["hx_uuid"];
            $hx_username = $return_lq_member["hx_username"];
            $hx_userpass = $return_lq_member["hx_password"];
            //$hx_nickname = $return_lq_member["account"];
            $role = $return_lq_member["role"];
            //$address = $return_lq_member["address"];
            $token = $return_lq_member["token"];
            $token_status = $return_lq_member["token_status"];
            $token_time = $return_lq_member["token_time"];
            $add_time = $return_lq_member["add_time"];
            $pushnews = $return_lq_member["pushnews"];
            $status = $return_lq_member["status"];
        }
        /*
        $this->load->model('liaogeqiu/member_model');
        $return = object_array($this->liaogeqiu_member_model->get5UMember($return_lq_member["member_id"]));
        */
        //此处模拟成功的信息
        $lq_reg_params = array();
        unset($result_5u_api["userid"], $result_5u_api["username"], $result_5u_api["state_desc"], $result_5u_api["state_code"]);
        $lq_reg_params = $result_5u_api;
        $lq_reg_params["member_id"] = $usport_userid;
        $lq_reg_params["account"] = $username;
        $lq_reg_params["hx_uuid"] = $hx_uuid;
        $lq_reg_params["hx_username"] = $hx_username;
        $lq_reg_params["hx_password"] = $hx_userpass;
        $lq_reg_params["role"] = $role;
        //$lq_reg_params["address"] = $address;
        $lq_reg_params["token"] = $token;
        $lq_reg_params["token_status"] = $token_status;
        $lq_reg_params["token_time"] = $token_time;
        $lq_reg_params["add_time"] = $add_time;
        $lq_reg_params["pushnews"] = $pushnews;
        $lq_reg_params["status"] = $status;
        $lq_reg_params['member_logo'] = !empty($member_logo) ? $member_logo : $result_5u_api["member_logo"];

        $lq_reg_params['fans_count'] = get_fans_count($usport_userid);
        $lq_reg_params['follows_count'] = get_follows_count($usport_userid);
        $lq_reg_params['focusteam'] = get_focusteam($usport_userid);
        $lq_reg_params['album'] = get_album($usport_userid);
        $lq_reg_params['qcloud_sig'] = qcloud_sig($usport_userid);
                
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "登录成功";
        $this->res["token"] = $token;
        $this->res["user_detail"] = $lq_reg_params;
        output($this->res);

    }

    /**
     * 获取第三方头像地址
     * 
     * @param string $member_logo
     * @return boolean
     */
    public function get_member_logo($member_logo, $member_id)
    {
        if(empty($member_logo))
        {
            return FALSE;
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $member_logo);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.1; SV1; InfoPath.2)");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,  0);
        
        $content = curl_exec($curl);
        $errorno = curl_error($curl);
        curl_close($curl);
        
        if($errorno)
        {
            return FALSE;
        } 
        
        $upload_dir = get_config_field('avatar_api_dir');
        passport_avatar_home_set($member_id, $upload_dir.'data'.DS.'avatar');
        $middle_file = passport_avatar_file($member_id, 'middle', TRUE, FALSE);
        $handle = @fopen($middle_file, 'wb');
        if(!$handle)
        {
            return FALSE;
        }
        
        $fwrite = fwrite($handle, $content);
        if ($fwrite === FALSE)
        {
            return FALSE;
        }
        
        fclose($handle);
        $url = get_access_path(trim(str_replace(APPPATH, '', $middle_file), '.')) ;
        return $url;
    }
}
