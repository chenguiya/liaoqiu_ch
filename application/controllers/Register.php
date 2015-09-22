<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 会员注册（手机/邮箱/第三方平台等）
 *
 * @author xiaoquanji
 */
class Register extends CI_Controller
{
    private $res = array(
        "state_code" => -99,
        "state_desc" => "非法提交",
    );
    public $ignore_verify = array(
        'registerbyemail', 'registerbyoauthemail', ''
    );
    public function __construct() 
    {
        parent::__construct();
        if(!in_array(get_action(), $this->ignore_verify))
        {
            verify_sign();
        }
        $this->load->model("liaoqiu_member_model");
        $this->load->helper('huanxin_helper');
        
    }
    
    
    /**
     * 会员app邮箱注册
     * 
     */
    public function registerbyemail($email, $password) 
    {
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        $email = urldecode($email);
        $password = urldecode($password);

        try 
        {
            //判断参数完整性
            if(!isset($email) || !isset($password))
            {
                _E('参数错误', 11010);
            }
            if(!check_email($email))
            {
                _E("邮箱格式错误", 11001);
            }
            elseif (!is_password($password)) 
            {
                _E('密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素', 11002);
            }
                
            $params = array();
            $params['email'] = $email;
            $params['password'] = $password;
                
            //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
            $result = json_decode(usport_api("register_email_1", $params), true);
            if(!empty($result) && !empty($result['state_code']))
            {
                _E($result['state_desc'], $result['state_code']);
            }
            
            //自动注册第二步
            $this->registerstep2($result['token'], auto_create_nickname($email, 1));
        
            
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }

        output($result);
    }
    
    
    
    /**
     * 会员注册第二步(支持邮箱、手机、第三方平台)
     * 
     */
    public function registerstep2($token, $nickname) 
    {
        $this->register_step2($token, $nickname, Liaoqiu_member_model::ROLE_NORMAL);
    }

    /**
     * 会员注册第二步（提供给后台使用）
     * 
    */
    public function register_step2($token, $nickname, $role) 
    {
        $token = urldecode($token);
        $nickname = urldecode($nickname);
        $role = intval($role);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
                if(!array_key_exists($role, Liaoqiu_member_model::$roles_arr))
                {
                    _E('角色错误', 11020);
                }
                //判断参数完整性
                if(!isset($token) || !isset($nickname))
                {
                    _E('参数错误', -99);
                }
                
                if(!check_token($token))
                {
                    _E('令牌错误' , 11021);
                }
                
                if(!check_nickname($nickname))
                {
                    _E('昵称格式错误，2-20位字符，支持汉字、数字、字母', 11022);
                }
                
                $params = array();
                $params["nickname"] = $nickname;
                $params["token"] = $token;

                //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
                $result_5u_api = json_decode(usport_api("register_step_2", $params), true);
                //echo "<pre>"; print_r($result_5u_api);echo "</pre>";die;

                if($result_5u_api["state_code"]!="0" || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" || $result_5u_api["member_logo"]=="") 
                {
                    output($result_5u_api);
                }

                $bool_hx_reg_result = true;
                $result_5u_api['hx_username'] = $result_5u_api['hx_userpass'] = '';
                $huanxin_result = batchregister_huanxin($result_5u_api, $bool_hx_reg_result, $result_5u_api['hx_username'], $result_5u_api['hx_userpass']);
                //echo "<pre>"; print_r($huanxin_result);echo "</pre>";
                
                if(!$bool_hx_reg_result)
                {
                    $error_msg = $huanxin_result["huanxin_result"]["error"];
                    if("duplicate_unique_property_exists" == $error_msg)
                    {
                        $error_msg = "账号已存在";
                    }
                    
                    _E($error_msg, -4);
                }

                //step4: 环信注册成功, 写聊球会员表, 取token令牌，返回给客户端
                $token = '';
                $liaogeqiu_result = $this->liaoqiu_member_model->insertMember($result_5u_api, $huanxin_result, $token);
                if($liaogeqiu_result == -2)
                {
                    _E('用户已存在', 11029);
                }
                
                unset($result_5u_api["state_code"], $result_5u_api["state_desc"], $result_5u_api["username"], $result_5u_api["userid"], $result_5u_api['hx_username'] , $result_5u_api['hx_userpass']);
                //unset($lq_reg_params["hx_password"]);
                #$lq_reg_params = array_merge($lq_reg_params, $result_5u_api);
                
                //获取登录信息
                $member = getuser_by_token($token );
                $member_id = $member['member_id'];
                $result_5u_api = getinfo_byuserid($member_id);
                $result_5u_api['user_detail'] = add_account_hx_username($result_5u_api['user_detail']);
                $result_5u_api = array_merge($member, array_pop($result_5u_api['user_detail']));
                $result_5u_api['fans_count'] = get_fans_count($member_id);
                $result_5u_api['follows_count'] = get_follows_count($member_id);
                $result_5u_api['focusteam'] = get_focusteam($member_id);
                $result_5u_api['album'] = get_album($member_id);
                $result_5u_api['qcloud_sig'] = qcloud_sig($member_id);
                
                //此处模拟成功的信息
                $result["state_code"] = 0;
                $result["state_desc"] = "注册成功";
                $result["token"] = $token;
                $result["user_detail"] = $result_5u_api;
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }

    



    /**
     * 第三方平台注册接口（邮箱注册）
     * 
     */
    public function registerbyoauthemail($email, $password, $oauth_type, $oauth_id, $oauth_head) 
    {
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            $result = $this->registerbyoauth("1", $email, $password, $oauth_type, $oauth_id, $oauth_head, "");
            if(!empty($result) && !empty($result['state_code']))
            {
                _E($result['state_desc'], $result['state_code']);
            }
            
            
            //自动注册第二步
            $this->registerstep2($result['token'], auto_create_nickname($email, 1));
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }

    /**
     * 第三方平台注册接口（手机注册）
     * 
     */
    public function registerbyoauthphone($phone_token, $verify_code, $password, $oauth_type, $oauth_id, $oauth_head) 
    {
        
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        try
        {
            
            if(countstring($verify_code)!=6 || !is_num($verify_code)) 
            {
                _E('验证码格式错误', 11050);
            }
            
            $result = $this->registerbyoauth("2", $phone_token, $password, $oauth_type, $oauth_id, $oauth_head, $verify_code);
            if(!empty($result) && !empty($result['state_code']))
            {
                _E($result['state_desc'], $result['state_code']);
            }
            
            
            //自动注册第二步
            $this->registerstep2($result['token'], auto_create_nickname($email, 1));
            
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        output($result);
    }

    /**
     * 第三方平台注册接口
     * 
     */
    private function registerbyoauth($type, $params1, $password, $oauth_type, $oauth_id, $oauth_head, $params2 = "") 
    {
        
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        try
        {
            
            $type = urldecode($type);
            $params1 = urldecode($params1);
            $oauth_type = urldecode($oauth_type);
            $oauth_id = urldecode($oauth_id);
            $oauth_head = urldecode($oauth_head);
            $password = urldecode($password);

            $arr_oauth_type = array(
                "1" => "qq",
                "2" => "sina",
                "3" => "weixin",
            );

            $arr_reg_type = array(
                "1" => "邮箱注册",
                "2" => "手机注册",
            );

            if(!array_key_exists($type, $arr_reg_type))
            {    
                _E('type格式错误', 11060);
            }

            if(!array_key_exists($oauth_type, $arr_oauth_type))
            {
                _E('第三方类型错误', 11061);
            }

            if(!isset($params1) || !isset($password) || !isset($oauth_id))
            {
                _E('参数错误', -99);
            }
            //判断参数完整性
            if(($type == "1") && !check_email($params1))
            {
                _E('邮箱格式错误', 11080);
            }
                
            if(!is_password($password)) 
            {
                _E('密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素', 11081);
            }
                

            $params = array();
            $params["type"] = $type;
            
            if($type=="1") 
            {
                $params["email"] = $params1;
            }
            
            if($type=="2") 
            {
                $params["token"] = $params1;
                $params["verify_code"] = $params2;
            }
            
            $params["password"] = $password;
            $params["from"] = $arr_oauth_type[$oauth_type];
            $params["connectid"] = $oauth_id;
            $params["remote_avatar"] = $oauth_head;

            //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
            $result = json_decode(usport_api("register_oauth", $params), true);
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        return $result;
        //output($result);
    }
    
    
    /**
     * 会员app手机号注册
     * 
     */
    public function registerbyphone($token, $mobile, $verify_code, $password) 
    {
        $token = urldecode($token);
        $mobile = urldecode($mobile);
        $verify_code = urldecode($verify_code);
        $password = urldecode($password);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try 
        {
            if(!isset($password) || !isset($token) || !isset($mobile))
            {
                _E('参数错误', -99);
            }
            
            if(!check_mobile($mobile))
            {
                _E('手机号格式错误', 11089);
            }
            if(countstring($verify_code)!=6 || !is_num($verify_code)) 
            {
                _E('验证码格式错误', 11090);
            }
            
            if(!is_password($password)) 
            {
                _E('密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素', 11091);
            }
            
            $params = array();
            $params["token"] = $token;
            $params["verify_code"] = $verify_code;
            $params["password"] = $password;

            //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
            $result = json_decode(usport_api("register_mobile_1", $params), true);
            if(!empty($result) && !empty($result['state_code']))
            {
                _E($result['state_desc'], $result['state_code']);
            }
            
            //自动注册第二步
            $this->registerstep2($result['token'], auto_create_nickname($mobile, 2));
        
        } 
        catch (Exception $ex) 
        {   
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }

    
    
    //===============================================旧版   暂未修改===================================================================================
    
    //会员app传统注册（用户名注册）
    public function register($email, $username, $password, $sex) {

        $email = urldecode($email);
        $username = urldecode($username);
        $password = urldecode($password);

        //判断参数完整性
        $email_patern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        $uname_patern = "/^[a-zA-Z0-9_\x7f-\xff][@\.a-zA-Z0-9_\x7f-\xff]+$/";

        if(isset($email) && isset($username) && isset($password) && isset($sex))
        {
            if(!preg_match($email_patern, $email)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "邮箱格式错误";
                output($this->res);
            }
            else if(!preg_match($uname_patern, $username) || countstring($username)>20 || countstring($username)<2) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "账号格式错误，2-20位数字，字母组合";
                output($this->res);
            }
            else if(!is_password($password)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素";
                output($this->res);
            }
            else if($sex!=0 && $sex!=1 && $sex!=2) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "性别错误";    //0为保密，1为男，2为女
                output($this->res);
            }
            $sex = intval($sex);
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $params = array();
        $params["email"] = $email;
        $params["sex"] = $sex;
        $params["username"] = $username;
        $params["password"] = $password;

        //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
        $result_5u_api = json_decode(usport_api("register_member", $params), true);

        //如果是返回9，用户名已经存在, 轮到我们这边判断是否存在了
        if(($result_5u_api["state_code"]!="0" && $result_5u_api["state_code"]!="9") || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" || $result_5u_api["member_logo"]=="") {
            $this->res["state_code"] = $result_5u_api["state_code"];
            $this->res["state_desc"] = $result_5u_api["state_desc"];
            output($this->res);
        }

        if($result_5u_api["state_code"]=="9"){
            $liaogeqiu_result = $this->liaoqiu_member_model->getMemberByMemberID($result_5u_api["userid"]);
            //张建华那边已存在，并且聊球这边也存在
            if(count($liaogeqiu_result) > 0){
                $this->res["state_code"] = -4;
                $this->res["state_desc"] = "用户已经存在";
                output($this->res);
            }
        } else {
            //张建华那边不存在的，需要新注册的，这里的地址，昵称，心情都是初始值
            $result_5u_api["address"] = "";
            $result_5u_api["signiture"] = "";
        }

        //step3: 5U张建华注册成功或者张建华那边存在账号，但聊球不存在账号，这里去环信注册
        $usport_userid = $result_5u_api["userid"];
        $hx_username = "5usport_".$usport_userid;
        $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);
        $member_logo = $result_5u_api["member_logo"];

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
            $error_msg = $huanxin_result["huanxin_result"]["error"];
            if("duplicate_unique_property_exists" == $error_msg){
                $error_msg = "账号已存在";
            }
            $this->res["state_desc"] = $error_msg;
            output($this->res);
        }

        if($result_5u_api["state_code"]=="9"){
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "用户已经存在";
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


        $liaogeqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);

        if($liaogeqiu_result == -2) {
            $this->res["state_code"] = -2;
            $this->res["state_desc"] = "用户已存在";
            output($this->res);
        } else {
            unset($result_5u_api["state_code"]);
            unset($result_5u_api["state_desc"]);
            unset($result_5u_api["username"]);
            unset($result_5u_api["userid"]);
            //unset($lq_reg_params["hx_password"]);
            $lq_reg_params = array_merge($lq_reg_params, $result_5u_api);
        }

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "注册成功";
        $this->res["token"] = $token;
        $this->res["user_detail"] = $lq_reg_params;

        //echo "<pre>";print_R($this->res);

        output($this->res);
    }

    //会员app邮箱注册(一步到位)
    public function registerbyemail1($email, $password, $nickname) {
        $email = urldecode($email);
        $nickname = urldecode($nickname);
        $password = urldecode($password);
        $sex = 0;

        //判断参数完整性
        $email_patern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        $nickname_patern = "/[a-zA-Z0-9\u4e00-\u9fa5]{2,20}/";

        if(isset($email) && isset($nickname) && isset($password) && isset($sex))
        {
            if(!preg_match($email_patern, $email)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "邮箱格式错误";
                output($this->res);
            }
            else if(!preg_match($nickname_patern, $nickname) || countstring($nickname)>20 || countstring($nickname)<2) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "昵称格式错误，2-20位字符，支持汉字、数字、字母";
                output($this->res);
            }
            else if(!is_password($password)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素";
                output($this->res);
            }
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $params = array();
        $params["email"] = $email;
        $params["sex"] = $sex;
        $params["nickname"] = $nickname;
        $params["password"] = $password;

        //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
        $result_5u_api = json_decode(usport_api("register_member", $params), true);

        if($result_5u_api["state_code"]!="0" || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" || $result_5u_api["member_logo"]=="") {
            output($result_5u_api);
        }

        $username = $result_5u_api["username"];
        $usport_userid = $result_5u_api["userid"];
        $hx_username = "5usport_".$usport_userid;
        $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);
        $member_logo = $result_5u_api["member_logo"];

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
            $error_msg = $huanxin_result["huanxin_result"]["error"];
            if("duplicate_unique_property_exists" == $error_msg){
                $error_msg = "账号已存在";
            }
            $this->res["state_desc"] = $error_msg;
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


        $liaogeqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);

        if($liaogeqiu_result == -2) {
            $this->res["state_code"] = -2;
            $this->res["state_desc"] = "用户已存在";
            output($this->res);
        } else {
            unset($result_5u_api["state_code"], $result_5u_api["userid"], $result_5u_api["state_desc"], $result_5u_api["username"]);
            $lq_reg_params = array_merge($lq_reg_params, $result_5u_api);
        }

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "注册成功";
        $this->res["token"] = $token;
        $this->res["user_detail"] = $lq_reg_params;
        //echo "<pre>";print_R($this->res);
        output($this->res);
    }

    //会员app手机号注册（一步到位）
    public function registerbyphone1($token, $password, $nickname) {
        $token = urldecode($token);
        $nickname = urldecode($nickname);
        $password = urldecode($password);
        $sex = 0;

        //判断参数完整性
        $nickname_patern = "/[a-zA-Z0-9\u4e00-\u9fa5]{2,20}/";

        if(isset($nickname) && isset($password) && isset($sex))
        {
            if(!preg_match($nickname_patern, $nickname) || countstring($nickname)>20 || countstring($nickname)<2) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "昵称格式错误，2-20位字符，支持汉字、数字、字母";
                output($this->res);
            }
            else if(!is_password($password)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "密码格式错误；格式为：8-16位，且必须包含（字母，数字，符号）中至少两种元素";
                output($this->res);
            }
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $params = array();
        $params["token"] = $token;
        $params["sex"] = $sex;
        $params["nickname"] = $nickname;
        $params["password"] = $password;

        //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
        $result_5u_api = json_decode(usport_api("register_member", $params), true);

        if($result_5u_api["state_code"]!="0" || $result_5u_api["username"]=="" || $result_5u_api["userid"]=="" || $result_5u_api["member_logo"]=="") {
            output($result_5u_api);
        }

        $username = $result_5u_api["username"];
        $usport_userid = $result_5u_api["userid"];
        $hx_username = "5usport_".$usport_userid;
        $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);
        $member_logo = $result_5u_api["member_logo"];

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
            $error_msg = $huanxin_result["huanxin_result"]["error"];
            if("duplicate_unique_property_exists" == $error_msg){
                $error_msg = "账号已存在";
            }
            $this->res["state_desc"] = $error_msg;
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


        $liaogeqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);

        if($liaogeqiu_result == -2) {
            $this->res["state_code"] = -2;
            $this->res["state_desc"] = "用户已存在";
            output($this->res);
        } else {
            unset($result_5u_api["state_code"]);
            unset($result_5u_api["state_desc"]);
            unset($result_5u_api["username"]);
            unset($result_5u_api["userid"]);
            //unset($lq_reg_params["hx_password"]);
            $lq_reg_params = array_merge($lq_reg_params, $result_5u_api);
        }

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "注册成功";
        $this->res["token"] = $token;
        $this->res["user_detail"] = $lq_reg_params;
        //echo "<pre>";print_R($this->res);
        output($this->res);
    }

    //将5U体育的会员注册成为聊球会员
    public function register_from_5u($member_id, $username) {
        $username = urldecode($username);
        $uname_patern = "/[a-zA-Z0-9_@\.\x7f-\xff]{2, 30}/";
        //step1 数据校验
        if(isset($username))
        {
            if(!preg_match($uname_patern, $username)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "账号格式错误，2-20位，中文数字，字母组合";
                output($this->res);
            }
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //读取用户表
        $member = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
        $return_lq_member = object_array($member);
        //echo "<pre>"; print_R($return_lq_member);
        if(count($return_lq_member)==0) {
            //注册一个账号
            //step3: 去环信注册
            $usport_userid = $member_id;
            $hx_username = "5usport_".$usport_userid;
            $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);

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


            $liaogeqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);
            //此处模拟成功的信息
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "添加成功";
            //$this->res["token"] = $token;
            //$this->res["user_detail"] = $lq_reg_params;
            //echo "<pre>";print_R($this->res);
            output($this->res);
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此用户已经是聊球会员了";
            output($this->res);
        }
    }
}
