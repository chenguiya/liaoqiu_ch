<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Member extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "非法提交",
    );

    public $ignore_verify = array(
        'sendphoneverify', 'findpwd',
    );
    
    /**
     * 构造函数
     */
    public function __construct()
    {
     
        parent::__construct();
        $this->load->model('liaoqiu_member_model');
        $this->load->helper('huanxin_helper');
        if(!in_array(get_action(), $this->ignore_verify))
        {
            verify_sign();
        }
        
    }
    
    //手机号注册发送验证码接口
    public function sendphoneverify($type, $phone, $timestamp) {
        $phone = urldecode($phone);
        $type = urldecode($type);
        $timestamp = urldecode($timestamp);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            $arr_phone_type = array(
                "1" => "注册",
                "2" => "重置密码",
                "3" => "验证身份",
                "4" => "绑定新手机号",
            );

            if(!array_key_exists($type, $arr_phone_type))
            {
                _E('Type格式错误', 11030);
            }

            //判断参数完整性
            if(!isset($phone) || !isset($timestamp)) 
            {    
                _E('参数错误', -99);
            }

            $params = array();
            $params["phone"] = $phone;
            $params["type"] = $type;
            $params["timestamp"] = $timestamp;
            
            if(ENVIRONMENT == 'development')
            {
                $params['timestamp'] = time();
            }
            //step2: 通过接口，获取赛程。
            $result = json_decode(usport_api("send_phone_verify", $params), true);
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();

        }
        
        output($result);
    }

    //手机号校验验证码接口
    public function verifyregphone($token, $verify_code, $timestamp) {
        
        $token = urldecode($token);
        $verify_code = urldecode($verify_code);
        $timestamp = urldecode($timestamp);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($token) || !isset($verify_code) || !isset($timestamp)) 
            {
                _E('参数错误', -99);
            }
            
            $jiange = abs((time()*1000) - $timestamp);
            //时间间隔相差5分钟，表示此请求已过期
            if($jiange > 5*3600*1000)
            {
                _E('请求已过期，请重新请求', 11040);
            }

            $params = array();
            $params["token"] = $token;
            $params["verify_code"] = $verify_code;
            $params["timestamp"] = $timestamp;

            //step2: 通过接口，获取赛程。
            $result = json_decode(usport_api("verify_phone", $params), true);
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }


    //校验用户账号安全性
    public function verify_token($token) {
        
        try
        {
            //判断参数完整性
            if(!isset($token))
            {
                throw new Exception('参数错误', -99);
            }
            
            //读取用户表
            $result = getuser_by_token($token);
            
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "账号正常";
        } 
        catch (Exception $ex) 
        {
            $this->res["state_code"] = $ex->getCode();
            $this->res["state_desc"] = $ex->getMessage();
            
        }
        
        output($this->res);
    }

    //根据5U账号获取用户资料
    private function getinfo_by5uaccount($account) {
        $account = urldecode($account);
        /*
        $arr_account_tmp = explode("5usport_", $account);
        if(count($arr_account_tmp)!=2) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "账号不存在";
            output($this->res);
        }
        if(!is_int($arr_account_tmp[1]) || intval($arr_account_tmp[1])==0) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "账号不存在";
            output($this->res);
        }
        */
        $member = $this->liaoqiu_member_model->getMemberBy5Uaccount($account);
        $return_lq_member = object_array($member);
        return $return_lq_member;
    }

   

    //修改会员资料
    public function modify_info($token, $email, $nickname, $sex, $address, $signiture, $education, $income, $birthday) {
        $nickname = urldecode($nickname);
        $sex = urldecode($sex);
        $email = urldecode($email);
        $address = urldecode($address);
        $signiture = urldecode($signiture);
        $education = urldecode($education);
        $income = urldecode($income);
        $birthday = urldecode($birthday);
        $result = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($token)) 
            {
                throw new Exception('参数错误', -99);
            }

            $arr_params = array();
            
            if(strtoupper($email)!="NULL") 
            {
                $arr_params["email"] = $email;
            }
            
            if(strtoupper($nickname)!="NULL") 
            {
                $arr_params["nickname"] = $nickname;
            }
            
            if(strtoupper($sex)!="NULL") 
            {
                $sex = intval(0 + $sex);
                if(abs($sex)>2)
                {
                    throw new Exception('性别错误', 13010);
                }
                
                $arr_params["sex"] = $sex;
            }
            
            if(strtoupper($address)!="NULL") 
            {
                $arr_params["address"] = $address;
            }
            
            if(strtoupper($signiture)!="NULL") 
            {
                $arr_params["signiture"] = $signiture;
            }
            
            if(strtoupper($education)!="NULL") 
            {
                $arr_params["education"] = $education;
            }
            
            if(strtoupper($income)!="NULL") 
            {
                $arr_params["income"] = $income;
            }
            
            if(strtoupper($birthday)!="NULL") 
            {
                $arr_params["birthday"] = $birthday;
            }
            
            if(count($arr_params)==0) 
            {
                throw new Exception('没有传入修改项', 13011);
            }
            
            //校验token令牌
            $return_lq_member = getuser_by_token($token);
            $member_id = $return_lq_member["member_id"];
 
            $arr_params["userid"] = $member_id;
            $result = json_decode(usport_api("edit_member_info", $arr_params), true);
        } 
        catch (Exception $ex) 
        {
            $result['state_code'] = $ex->getCode();
            $result['state_desc'] = $ex->getMessage();
        }
        
        output($result);
    }


    //修改密码 http://libo.dev.usport.cc/5usport/liaogeqiu/member/modifypwd/293a3498dea329da85f2/oldpassword/newpassword/api?sign=sign签名
    public function modify_pwd($token, $oldpwd, $newpwd) {
        $oldpwd = urldecode($oldpwd);
        $newpwd = urldecode($newpwd);
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($token) || !isset($oldpwd) || !isset($newpwd)) 
            {
                throw new Exception('参数错误', -99);
            }

            $return_lq_member = getuser_by_token($token);
            $member_id =  $return_lq_member["member_id"];
            //修改密码
            $params = array();
            $params["userid"] = $member_id;
            $params["oldpwd"] = $oldpwd;
            $params["newpwd"] = $newpwd;
            $result_5u_api = json_decode(usport_api("change_password", $params), true);

            //此处模拟输出版本信息
            $return["state_code"] = $result_5u_api["state_code"];
            $return["state_desc"] = $result_5u_api["state_desc"];
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        output($return);
    }


    //找回密码（旧）
    public function findpwd($account, $email) {
        $account = urldecode($account);
        $email = urldecode($email);
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($email) || !isset($account)) 
            {
                throw new Exception('参数错误', -99);
            }
            //找回密码
            $params = array();
            $params["account"] = $account;
            $params["email"] = $email;
            $result_5u_api = json_decode(usport_api("find_password", $params), true);

            //此处模拟输出版本信息
            $return["state_code"] = $result_5u_api["state_code"];
            $return["state_desc"] = $result_5u_api["state_desc"];
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        output($return);
    }

    //重置密码__通过邮箱（新）
    public function resetpasswordbyemail($email) {
        $email = urldecode($email);
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($email)) 
            {
                throw new Exception('参数错误', -99);
            }
            //找回密码
            $params = array();
            $params["email"] = $email;
            $return = json_decode(usport_api("resetpass_email", $params), true);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        output($return);
    }

    //重置密码__通过手机号（新）
    public function resetpasswordbyphone($token, $password) {
        $token = urldecode($token);
        $password = urldecode($password);
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($token) || !isset($password)) 
            {
                throw new Exception('参数错误', -99); 
            }
            //找回密码
            $params = array();
            $params["token"] = $token;
            $params["password"] = $password;
            $return = json_decode(usport_api("resetpass_phone", $params), true);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        output($return);
    }
    //绑定新邮箱（需要密码）
    public function bindemail($token, $passwd, $email) {
        $email = urldecode($email);
        $token = urldecode($token);
        $passwd = urldecode($passwd);
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try 
        {
            //判断参数完整性
            if(!isset($token) || !isset($passwd) || !isset($email)) 
            {
                throw new Exception('参数错误', -99); 
            }
            
            //校验token
            $return_lq_member = getuser_by_token($token);
            $member_id =  $return_lq_member["member_id"];
            //找回密码
            $params = array();
            $params["email"] = $email;
            $params['userid'] = $member_id;
            $params['password'] = $passwd;
            
            $result_5u_api = json_decode(usport_api("bind_email", $params), true);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        output($return);
    }

    //绑定新手机号（需要密码）
    public function bindphone($token1, $passwd, $token2, $verify_code) {
        $token1 = urldecode($token1);
        $token2 = urldecode($token2);
        $verify_code = urldecode($verify_code);
        //判断参数完整性
        if(!isset($token1) || !isset($token2) || !isset($verify_code)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //校验Token
        $return_lq_member = getuser_by_token($token1);
        $member_id = $return_lq_member['member_id'];
        //找回密码
        $params = array();
        $params["userid"] = $member_id;
        $params["token"] = $token2;
        $params["verify_code"] = $verify_code;
        $params['password'] = $passwd;
        $result_5u_api = json_decode(usport_api("bind_phone", $params), true);
        output($result_5u_api);
    }


    //绑定新邮箱（不需要密码）
    public function bindemail2($token, $email) {
        $token = urldecode($token);
        $email = urldecode($email);
        //判断参数完整性
        if(!isset($token) || !isset($email)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //校验token
        $return_lq_member = getuser_by_token($token);
        $member_id =  $return_lq_member["member_id"];
        //找回密码
        $params = array();
        $params["userid"] = $member_id;
        $params["email"] = $email;
        $result_5u_api = json_decode(usport_api("bind_email2", $params), true);
        output($result_5u_api);
    }

    //绑定新手机号（不需要密码）
    public function bindphone2($token, $phone_token, $verify_code) {
        $token = urldecode($token);
        $phone_token = urldecode($phone_token);
        $verify_code = urldecode($verify_code);
        //判断参数完整性
        if(!isset($token) || !isset($phone_token) || !isset($verify_code)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //校验token
        $return_lq_member = getuser_by_token($token);
        $member_id =  $return_lq_member["member_id"];
        //找回密码
        $params = array();
        $params["userid"] = $member_id;
        $params["token"] = $phone_token;
        $params["verify_code"] = $verify_code;
        $result_5u_api = json_decode(usport_api("bind_phone2", $params), true);
        output($result_5u_api);
    }

    //获取用户邮箱手机的绑定状态接口
    public function getbindstate($token) {
        $token = urldecode($token);
        //判断参数完整性
        if(!isset($token)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //校验token
        $return_lq_member = getuser_by_token($token);
        $member_id =  $return_lq_member["member_id"];

        $member_info = getinfo_byuserid($member_id);
        if($member_info["state_code"]!="0") {
            output($member_info);
        }
        $member_info = $member_info["user_detail"][0];
        //echo "<pre>"; print_r($member_info);echo "</pre>";
        if($member_info["email"]==""){
            $email = null;
        } else {
            $email = $member_info["email"];
        }
        if($member_info["mobile_phone"]==""){
            $mobile_phone = null;
        } else {
            $mobile_phone = $member_info["mobile_phone"];
        }
        $arr_ret = array();
        $arr_ret["state_code"] = 0;
        $arr_ret["state_desc"] = "操作成功";
        $arr_ret["email"] = array(
            "bind_email" => $member_info["bind_email"],
            "email" => $email,
        );
        $arr_ret["mobile_phone"] = array(
            "bind_mobile" => $member_info["bind_mobile"],
            "mobile_phone" => $mobile_phone,
        );
        /*
        $params = array();
        $params["userid"] = $member_id;
        $result_5u_api = json_decode(usport_api("getbindstate", $params), true);
        output($result_5u_api);
        */
        output($arr_ret);
    }

    //搜索联系人
    public function search($token) {
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        
        try
        {
            //判断参数完整性
            if(!isset($token)) 
            {
                throw new Exception('参数错误', -99); 
            }
            $keyword = urldecode($_REQUEST["keyword"]);

            $arr_params = array();
            if(isset($_REQUEST["agemin"])) 
            {
                $arr_params["age_min"] = $_REQUEST["agemin"];
            }
            
            if(isset($_REQUEST["agemax"])) 
            {
                $arr_params["age_max"] = $_REQUEST["agemax"];
            }
            
            if(isset($_REQUEST["sex"])) 
            {
                $arr_params["sex"] = $_REQUEST["sex"];
            }
            
            if(isset($_REQUEST["type"]) && isset($keyword) && $_REQUEST["type"]=="1") 
            {
                $arr_params["type"] = $_REQUEST["type"];
                $arr_params["username"] = $keyword;
            }
            
            if(count($arr_params)==0) 
            {
                throw new Exception('没有传入搜索项', 14011);
            }

            $arr_params["age_min"] = $_REQUEST["agemin"];
            $arr_params["age_max"] = $_REQUEST["agemax"];
            $arr_params["sex"] = $_REQUEST["sex"];
            $arr_params["type"] = $_REQUEST["type"];
            $arr_params["username"] = $keyword;

            //校验token
            $return_lq_member = getuser_by_token($token);
            $member_id =  $return_lq_member["member_id"];

            //http://zhangjh.dev.usport.cc/api/liaoqiu/search_member?age_min=&age_max=&sex=&username=&sign=dd6b98602efedad4e3d76a842f6b1052
            //先校验令牌token, 并获取用户信息
            $return_lq_member = getuser_by_token($token);

            //这里通过张建华提供的接口，查询账号列表
            $result_5u_api = json_decode(usport_api("search_member", $arr_params), true);
            
            $search_list = array();
            if(!empty($result_5u_api['search_list']))
            {
                $search_list = $result_5u_api["search_list"];
            }
            
            //echo "<pre>"; print_r($search_list);echo "</pre>";
            $liaogeqiu_search_result = array();
            $member_id = "";
            if(count($search_list)>0){
                foreach ($search_list as $k => $v) {
                    //Do something here.
                    $member_id .= $v["userid"];
                    if($k < count($search_list) - 1){
                        $member_id .= ",";
                    }
                }
                $manyMember = $this->liaoqiu_member_model->getMemberByManyMemberID($member_id);
                $liaogeqiu_search_result = object_array($manyMember);
            }

            foreach ($liaogeqiu_search_result as $k => $v) {
                //Do something here.
                foreach ($search_list as $k1 => $v1) {
                    //Do something here.
                    if($v["member_id"] == $v1["userid"]){
                        $liaogeqiu_search_result[$k] = array_merge($v, $v1);
                        unset($liaogeqiu_search_result[$k]["hx_password"]);
                    }
                }
            }
            
            $arr_result = array();
            //echo "<pre>"; print_r($liaogeqiu_search_result);echo "</pre>";
            foreach ($liaogeqiu_search_result as $k => $v) {
                //Do something here.
                $arr_result[] = lq_member_init($v);
            }

            $return["state_code"] = 0;
            $return["state_desc"] = "搜索完成";
            $return["search_list"] = $arr_result;
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        output($return);
    }

    //添加黑名单
    public function addblacklist($token, $hx_account) {
        $hx_account = urldecode($hx_account);
        //判断参数完整性
        if(!isset($token) || !isset($hx_account)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //先校验令牌token, 并获取用户信息
        $return_lq_member = getuser_by_token($token);
        $bid = hxto5uID($hx_account);
        
        $aid = $return_lq_member["member_id"];

        //查查好友表，只要是好友才能拉黑
        $this->load->model('liaoqiu_friendship_model');
        $return_lq_member = $this->liaoqiu_friendship_model->getRecordByAIDBID($aid, $bid);
        //$return_lq_member = object_array($friendship);
        $return_lq_member = !empty($return_lq_member) ? $return_lq_member : array();
        if(count($return_lq_member) == 0) {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "操作失败，原因：".$hx_account."不是你的好友，不能进行此操作";
            output($this->res);
        }
        if($return_lq_member["status"]=="0") {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "操作失败，原因：".$hx_account."已经不是你的好友了，不能进行此操作";
            output($this->res);
        }

        //查询黑名单表，是否有记录，如果有，就更新，没有就插入
        //向环信拉黑
        $hx_action = "blackuser";
        $liboparams = array("from" => "5usport_".$aid, "to"=> "5usport_".$bid);
        $huanxin_result = huanxin($hx_action, $liboparams);

        $bool_hx_reg_result = true;
        if($huanxin_result["code"]!="0" || !array_key_exists("data", $huanxin_result["huanxin_result"])) {
            $bool_hx_reg_result = false;
        } else if(count($huanxin_result["huanxin_result"]["data"])!=1) {
            $bool_hx_reg_result = false;
        }
        //echo "<pre>"; print_r($huanxin_result);echo "</pre>";

        if(!$bool_hx_reg_result) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "拉黑失败，请稍候重试";
            output($this->res);
        }
        $this->load->model('liaoqiu_blacklist_model');
        $backlist = $this->liaoqiu_blacklist_model->getRecordByAIDBID($aid, $bid);
        $return_lq_member = !empty($backlist) ? $backlist : array();
        //$return_lq_member = object_array($backlist);

        if(!empty($return_lq_member) && ($return_lq_member["status"]=="1")) {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "该用户已经被你添加到黑名单了，无需重复操作";
            output($this->res);
        }
        //没有拉黑，则插入，有关系 则更新
        $add_time = time();
        $arr_params1 = array("a_id"=> $aid, "b_id"=> $bid, "add_time"=> $add_time, "status"=> "1");
        //echo "<pre>";print_R($arr_params1);

        //单向拉黑
        $model_result1 = $this->liaoqiu_blacklist_model->setRecord($arr_params1);
        //$model_result1 = object_array($backlist);
        //echo $model_result1;
        //$model_result2 = object_array($this->liaogeqiu_blacklist_model->setRecord($arr_params2));
        //echo $model_result2;
        //此处模拟输出版本信息
        if($model_result1 > 0) {
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "操作成功";
        } else {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试";
        }
        output($this->res);
    }

    //获取黑名单列表
    public function getblacklist($token) {
        $app_type = isset($_REQUEST["app_type"]) ? $_REQUEST["app_type"] : 0;
        //判断参数完整性
        if(!isset($token)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //先校验令牌token, 并获取用户信息
        $return_lq_member = getuser_by_token($token);
        $member_id = $return_lq_member["member_id"];

        //去环信获取黑名单列表；并同步到本地数据库
        $hx_action = "showblacklist";
        $liboparams = array("user" => "5usport_".$member_id);
        $huanxin_result = huanxin($hx_action, $liboparams);
        //echo "<pre>"; print_r($huanxin_result);echo "</pre>";

        if(!array_key_exists("data", $huanxin_result["huanxin_result"])) {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "获取黑名单列表失败，请稍候重试";
            output($this->res);
        }

        $arr_hx_blacklist = $huanxin_result["huanxin_result"]["data"];

        foreach ($arr_hx_blacklist as $k => $v) {
            //Do something here.
            if(!preg_match('|^5usport_[0-9]+$|', $v)){
                //echo $v;
                //不符合规范的账号要被过滤掉
                unset($arr_hx_blacklist[$k]);
            }
        }
        
        if(count($arr_hx_blacklist)==0){
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "黑名单为空";
            $this->res["black_list"] = null;
            output($this->res);
        }

        //echo "<pre>"; print_r($arr_hx_blacklist);echo "</pre>";
        $this->load->model('liaoqiu_blacklist_model');
        $add_time = time();
        $arr_member_id = array();
        foreach ($arr_hx_blacklist as $k => $v) {
            //Do something here.
            $arr_tmp = explode("5usport_", $v);
            $v = $arr_tmp[1];
            $arr_member_id[] = $v;
            $arr_params1 = array("a_id"=> $member_id, "b_id"=> $v, "add_time"=> $add_time, "status"=> "1");
            $arr_params2 = array("a_id"=> $v, "b_id"=> $member_id, "add_time"=> $add_time, "status"=> "1");
            //echo "<pre>";print_R($arr_params1);
            //单向拉黑好友
            $backlist = $this->liaoqiu_blacklist_model->setRecord($arr_params1);
            $black_result1 = object_array($backlist);
            //echo $black_result1;
            //$black_result2 = object_array($this->liaogeqiu_blacklist_model->setRecord($arr_params2));
            //echo $black_result2;
            //此处模拟输出版本信息
            if($black_result1 < 0) {
                // || $black_result2 < 0) {
                $this->res["state_code"] = -4;
                $this->res["state_desc"] = "服务器繁忙，请稍候重试";
                output($this->res);
            }
        }

        //根据ID，获取黑名单ID列表
        /*旧版获取黑名单方法，旧版以数据库为依归，新版以环信数据为依归
        $return_lq_member = object_array($this->liaogeqiu_blacklist_model->getRecordsByAID($return_lq_member["member_id"]));
        if(count($return_lq_member)==0) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "黑名单为空";
            output($this->res);
        }

        foreach ($return_lq_member as $k => $v) {
            $arr_member_id[] = $v["b_id"];
        }
        */

        //echo "<pre>"; print_R($arr_member_id); echo "</pre>";
        $arr_blacklist = getinfo_byuserid($arr_member_id);
        $arr_role = $this->getrolebyuserid($arr_member_id);
        //echo "<pre>"; print_R($arr_memberinfo_return); echo "</pre>";
        if($arr_blacklist["state_code"]!="0") {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试！".$arr_blacklist["state_desc"];
            output($this->res);
        }
        $arr_blacklist = $arr_blacklist["user_detail"];
        $arr_blacklist = add_account_hx_username($arr_blacklist);
        /*
        foreach ($arr_blacklist as $k => $v) {
            //Do something here.
            $v["member_logo"] = $v["logo"];
            $v["nick_name"] = $v["nickname"];
            unset($v["logo"]);
            unset($v["nickname"]);
            $arr_result[] = $v;
        }*/
        if($app_type == "2"){
            foreach ($arr_blacklist as $k => $v) {
                //Do something here.
                $v["role"] = isset($arr_role[$v["member_id"]]) ? $arr_role[$v["member_id"]]["role"] : $this->liaoqiu_member_model->getDefaultRole();
                $arr_result[] = lq_member_init($v);
            }
        } else {
            foreach ($arr_blacklist as $k => $v) {
                //Do something here.
                $v["role"] = isset($arr_role[$v["member_id"]]) ? $arr_role[$v["member_id"]] : $this->liaoqiu_member_model->getDefaultRole();
                $arr_result[Getzimu($v["nick_name"])][] = lq_member_init($v);
            }
            //echo "<pre>"; print_r($arr_result);echo "</pre>";
            ksort($arr_result);
        }

        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["black_list"] = $arr_result;
        output($this->res);
    }

    //移除黑名单
    public function removeblacklist($token, $hx_account) {
        $hx_account = urldecode($hx_account);
        $type = "0";
        //判断参数完整性
        if(!isset($token) || !isset($hx_account) || !isset($type) || ($type!="1" && $type!="0")) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //先校验令牌token, 并获取用户信息
        $return_lq_member = getuser_by_token($token);

        $bid = hxto5uID($hx_account);

        //查询好友关系表，是否有记录，如果有，就更新，没有就插入
        $aid = $return_lq_member["member_id"];

        $this->load->model('liaoqiu_blacklist_model');
        $backlist = $this->liaoqiu_blacklist_model->getRecordByAIDBID($aid, $bid);
        $return_lq_member = object_array($backlist);
        //echo "<pre>"; print_R($return_lq_member); die;
        if(count($return_lq_member) == 0) {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "操作失败，原因：".$hx_account."不在你的黑名单列表";
            output($this->res);
        }
        if($return_lq_member["status"]=="0") {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "已经将好友移除了黑名单，无需重复操作";
            output($this->res);
        }

        //将环信移除黑名单
        $hx_action = "delblackuser";
        $liboparams = array("from" => "5usport_".$aid, "to"=> "5usport_".$bid);
        $huanxin_result = huanxin($hx_action, $liboparams);

        $bool_hx_reg_result = true;
        if($huanxin_result["code"]!="0" || !array_key_exists("entities", $huanxin_result["huanxin_result"])) {
            $bool_hx_reg_result = false;
        } else if(count($huanxin_result["huanxin_result"]["entities"])!=1) {
            $bool_hx_reg_result = false;
        }
        //echo "<pre>"; print_r($huanxin_result);echo "</pre>";

        if(!$bool_hx_reg_result) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "拉黑失败，请稍候重试";
            output($this->res);
        }

        //没有关系，则插入，有关系 则更新
        $add_time = time();
        $arr_params1 = array("a_id"=> $aid, "b_id"=> $bid, "add_time"=> $add_time, "status"=> $type);
        //$arr_params2 = array("a_id"=> $bid, "b_id"=> $aid, "add_time"=> $add_time, "status"=> $type);
        //echo "<pre>";print_R($arr_params1);
        $backlist = $this->liaoqiu_blacklist_model->setRecord($arr_params1);
        $model_result1 = object_array($backlist);
        //echo $model_result1;
        //$model_result2 = object_array($this->liaogeqiu_friendship_model->setRecord($arr_params2));
        //echo $model_result2;
        //此处模拟输出版本信息
        if($model_result1 > 0) {
            $this->res["state_code"] = 0;
            $this->res["state_desc"] = "操作成功";
        } else {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试";
        }
        output($this->res);
    }


    //获取好友详情
    public function getfrienddetail($token, $hx_account) {
        $hx_account = urldecode($hx_account);
        //判断参数完整性
        if(!isset($token) || !isset($hx_account)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //先校验令牌token, 并获取用户信息
        $return_lq_member = getuser_by_token($token);

        $bid = hxto5uID($hx_account);

        //查询好友关系表，是否有记录，如果有，就更新，没有就插入
        $aid = $return_lq_member["member_id"];

        $this->load->model('liaoqiu_friendship_model');
        $friendship = $this->liaoqiu_friendship_model->getRecordByAIDBID($aid, $bid);
        $return_lq_member = object_array($friendship);
        //echo "<pre>"; print_R($return_lq_member); die;
        if(count($return_lq_member) == 0) {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "操作失败，原因：".$hx_account."不是你的好友";
            output($this->res);
        }
        if($return_lq_member["status"]=="0") {
            $this->res["state_code"] = -1;
            $this->res["state_desc"] = "操作失败，原因：".$hx_account."已经不是你的好友了";
            output($this->res);
        }

        //根据userid获取用户信息
        $member_info = getinfo_byuserid($bid);
        $arr_role = $this->getrolebyuserid($bid);
        //echo "<pre>"; print_R($arr_memberinfo_return); echo "</pre>";
        if($member_info["state_code"]!="0") {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试！".$member_info["state_desc"];
            output($this->res);
        }
        $member_info = $member_info["user_detail"];

        foreach ($member_info as $k => $v) {
            //Do something here.
            $v["userid"] = isset($v["userid"]) ? $v["userid"] : NULL;
            $v["role"] = isset($arr_role[$v["userid"]]) ? $arr_role[$v["userid"]]["role"] : $this->liaoqiu_member_model->getDefaultRole();;
            $arr_result[] = lq_member_init($v);
        }

        //此处模拟输出版本信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["friend_detail"] = $arr_result;
        output($this->res);
    }

    //http://libo.dev.usport.cc/5usport/liaogeqiu/member/getmanyuserdetail/token/5usport_20865usport_20875usport_2088/api?sign=sign签名
    //获取多个用户的详细信息
    public function getmanyuserdetail($token, $hx_account_list) {
        $hx_account_list = urldecode($hx_account_list);
        //判断参数完整性
        if(!isset($token) || !isset($hx_account_list)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //先校验令牌token, 并获取用户信息
        $return_lq_member = getuser_by_token($token);

        //校验用户账号列表合法性
        $member_id = deal_many_hxaccount($hx_account_list);
        //die($member_id);
        $arr_user_list = getinfo_byuserid($member_id);
        $arr_role = $this->getrolebyuserid($member_id);
        //echo "<pre>"; print_R($arr_memberinfo_return); echo "</pre>";
        if($arr_user_list["state_code"]!="0") {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $arr_user_list["state_desc"];
            output($this->res);
        }
        $arr_user_list = $arr_user_list["user_detail"];
        $arr_user_list = add_account_hx_username($arr_user_list);
        //echo "<pre>"; print_r($arr_user_list);echo "</pre>";
        //echo json_encode($arr_user_list);
        foreach ($arr_user_list as $k => $v) {
            //Do something here.
            $v["userid"] = isset($v["member_id"]) ? $v["member_id"] : '';
            $v["role"] = isset($arr_role[$v["member_id"]] ) ? $arr_role[$v["member_id"]]["role"] : $this->liaoqiu_member_model->getDefaultRole();
            $arr_result[] = lq_member_init($v);
        }

        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["user_list"] = $arr_result;
        output($this->res);
    }

    public function getrolebyuserid($arr_userid) {
        if(is_array($arr_userid)){
            $userid = implode(",", $arr_userid);
        } else {
            $userid = $arr_userid;
        }
        $arr_ret = array();
        $manyMember = $this->liaoqiu_member_model->getMemberInfoByManyMemberID($userid);
        $liaogeqiu_result = object_array($manyMember);
        //echo "<pre>"; print_r($liaogeqiu_result);echo "</pre>";
        if(!empty($liaogeqiu_result) && (count($liaogeqiu_result)>0) ){
            foreach ($liaogeqiu_result as $k => $v) {
                //Do something here.
                if($v["role"]==""){
                    $v["role"] = "1";
                }
                $arr_ret[$v["member_id"]] = $v;
            }
        }
        return $arr_ret;
    }
	
	public function getqcloudsign(){
		$token = $this->input->get('token', true);
                $hx_id = $this->input->get('hx_id', true);
                
		//判断参数完整性
                if(!empty($token)) 
                {
                    //校验token令牌
                    $result = getuser_by_token($token);
                    $member_id = $result['member_id'];
                }
                else
                {
                    $member_id = $hx_id;
                }
                $res = array('state_code'=>-10,'state_desc'=>'打开文件失败','qcloud_sig'=>array());
                $sig = qcloud_sig($member_id);
                if($sig)
                {
                    $res['state_code'] = 0;
                    $res['state_desc'] = '成功';
                    $res['qcloud_sig'] = $sig;
                }
                
                output($res);
		//$qcloud_url = "/data/www/5usport/application/controllers/liaogeqiu/qcloud/";
//		$qcloud_sig_dir = get_config_field('qcloud_sig_dir');
//                $qcloud_url = realpath($qcloud_sig_dir) . "/";
//                
//                $sig_url = $qcloud_url."user_sig/";
//                if(!file_exists($sig_url))
//                {
//                    mkdir($sig_url, 0777, true);
//                }
//                $url = $sig_url;
//		$fp = fopen($url.$member_id."_json",'w');
//		//echo 1;
//		if($fp) {
//			$json = '{
//					    "TLS.account_type": "169",
//					    "TLS.identifier": "'.$member_id.'",
//					    "TLS.appid_at_3rd": "1400000352",
//					    "TLS.sdk_appid": "1400000352",
//					    "TLS.expire_after": "86400"
//					}'; 
//			fwrite($fp,$json);
//			//$str_command ="/usr/local/tls_sig_api/lib/tls_licence_tools 1 ".$url."user_sig/".$member_id."_json ".$url."ec_key.pem ".$url."user_sig".$member_id."_sig";
//			//echo $str_command;
//			//$sig = exec("/usr/local/tls_sig_api/lib/tls_licence_tools 1 ".$sig_url.$member_id."_json ".$qcloud_url."ec_key.pem ".$sig_url.$member_id."_sig"); 
//			//print_r($sig);echo 2;
//			//dump($sig);die;
//		} 
//		else { 
//			$res = array('state_code'=>-10,'state_desc'=>'打开文件失败','qcloud_sig'=>array());
//			output($res); 
//		} 
//		fclose($fp);
		
	}
	
	public function isEmpty($value){//调用 $this->isEmpty($arr); //如果为空则终止
		if(empty($value)){  //如果为空，直接输出没有信息
				$res = array('state_code'=>0,'state_desc'=>'没有信息','qcloud_sig'=>array());
				output($res);
		}
	}
        
        /**
         * 上传相册
         * @param type $token
         */
        private function upload_photo($pic_path, $picname)
        {
            
            try
            {
                $token = $this->input->post('token', true);
                $return_member = getuser_by_token($token);
                //保存到相册
                $this->load->model('liaoqiu_member_photo_model');
                $params = array();
                $params['orig_filename'] = $picname;
                $params['file_path'] = $pic_path;
                $params['status'] = Liaoqiu_member_photo_model::STATUS_NORMAL;
                $params['member_id'] = $return_member['member_id'];
                $result = $this->liaoqiu_member_photo_model->setRecord($params);
                if(empty($result))
                {
                    throw new Exception('上传失败', 16011);
                }
            } 
            catch (Exception $ex) 
            {
                return FALSE;
            }
            
            return $result;
            return TRUE;
        }
        
        /**
         *  删除相册
         * 
         * @param string $token
         * @param int $fid
         */
        public function delete_photo($token, $fid)
        {
            $token = urldecode($token);
            $fid = urldecode($fid);
            $return = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try 
            {
                if(empty($token) || empty($fid))
                {
                    throw new Exception('参数错误', -99);
                }
                
                $member = getuser_by_token($token);
                $this->load->model('liaoqiu_member_photo_model');
                $photo = $this->liaoqiu_member_photo_model->getRecord($fid);
                
                if(empty($photo) || $photo['status'] == Liaoqiu_member_photo_model::STATUS_DELETE)
                {
                    throw new Exception('相片不存在或已被删除', 15010);
                }
                
                if($photo['member_id'] != $member['member_id'])
                {
                    throw new Exception('没有权限删除相片', 15011);
                }
                
                $params = array();
                $params['status'] = Liaoqiu_member_photo_model::STATUS_DELETE;
                $result = $this->liaoqiu_member_photo_model->editByID($fid, $params);
                
                if(empty($result))
                {
                    throw new Exception('删除失败；相片不存在', 15012);
                }
                
                //删除文件
                $filename = "./" . $photo['file_path'];
                unlink($filename);
                $return['state_code'] = 0;
                $return['state_desc'] = '相片删除成功';
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
        }
	
        /**
         * 主播列表
         * 
         */
        public function attention_anchors($token)
        {
            
            $token = urldecode($token);
            $return = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try
            {
                if(empty($token))
                {
                    throw new Exception('参数错误', 15020);
                }
                
                $member = getuser_by_token($token);
                
                $my_follows = $this->get_my_follow($member['member_id']);
                //主播列表
                $anchors = $result = array();
                if(!empty($my_follows))
                {
                    $anchors = $this->liaoqiu_member_model->getMemberList(array('role' => Liaoqiu_member_model::ROLE_ANCHOR, 'members' => $my_follows));
                }        
                $user_ids = array();
                if(!empty($anchors))
                {
                    
                    foreach ($anchors as $row)
                    {
                        $user_ids[] = $row['member_id'];
                    }
                    
                    $users = getinfo_byuserid($user_ids);
                    if($users['state_code'] != 0)
                    {
                        throw new Exception($users['state_desc'], $users['state_code']);
                    }
                    
                    $anchors = add_account_hx_username($users['user_detail']);
                    
                    foreach ($anchors as $row)
                    {
                        $member_id = $row['member_id'];
                        $row['follow'] = Liaoqiu_friendship_model::FOLLOW_NORMAL;
                        //$row['member_logo'] = passport_avatar_show($member_id, 'middle', TRUE);
                        $result[$member_id] = $row;
                    }
                    unset($anchors);
                }
                
                //关注我
                $follow_my = array();
                if(!empty($user_ids))
                {
                    $follow_my = $this->get_follow_my($user_ids, $member['member_id']);    
                }
                
                if(!empty($follow_my))
                {
                    foreach ($follow_my as $row)
                    {
                        if(isset($result[$row]))
                        {
                            $result[$row]['follow'] = Liaoqiu_friendship_model::FOLLOW_EACH;
                        }
                    }
                }
                
                $return['state_code'] = 0;
                $return['state_desc'] = '成功';
                $return['anchors_list'] = array_values($result);
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
        }
        
        /**
         * 获取关注我的
         * 
         * @param array $members
         * @param int $member_id
         * @param boolean $return_id
         */
        private function get_follow_my($members, $member_id, $return_id = true)
        {
            $return = array();
            load_model('friendship');
            $follows = $this->liaoqiu_friendship_model->getRecordsByBID($members, $member_id);
            
            if(!empty($follows) && !empty($return_id))
            {
                foreach ($follows as $follow)
                {
                    $return[] = $follow['a_id'];
                }
                
                return $return;
            }
            
            return $follows;
        }
        /**
         * 获取我关注的
         * 
         * @param type $member_id
         */
        private function get_my_follow($member_id, $members = array(), $return_id = true)
        {
            $return = array();
            load_model('friendship');
            $follows = $this->liaoqiu_friendship_model->getRecordsByBID($member_id, $members);
            
            if($return_id === true)
            {
                foreach ($follows as $follow)
                {
                    if($follow['status'] == Liaoqiu_friendship_model::STATUS_NORMAL)
                    {
                        $return[] = $follow['b_id'];
                    }
                }
                return $return;
            }
            
            return $follows;
        }

        /**
         * 粉丝列表
         * 
         * @param string $token
         */
        public function attention_fans($token)
        {
            
            $token = urldecode($token);
            $return = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try
            {
                if(empty($token))
                {
                    throw new Exception('参数错误', 15020);
                }
                
                $member = getuser_by_token($token);
                load_model('friendship');
                
                $follow_list = $fans_id = $fans = array();
                //关注我的。
                $fans = $this->get_follow_my(null, $member['member_id'], false);
                
                if(!empty($fans))
                {
                    $user_ids = array();
                    foreach ($fans as $row)
                    {
                        $user_ids[] = $row['a_id'];
                    }
                    
                    $users = getinfo_byuserid($user_ids);
                    if($users['state_code'] != 0)
                    {
                        throw new Exception($users['state_desc'], $users['state_code']);
                    }
                    
                    $anchors = add_account_hx_username($users['user_detail']);
                    
                    load_model('member');
                    foreach ($anchors as $row)
                    {
                        $member_id = $row['member_id'];
                        $fans_id[] = $member_id;
                        $row['follow'] = Liaoqiu_friendship_model::FOLLOW_ME;
                        $member_info = $this->liaoqiu_member_model->getMember($member_id);
                        
                        //$row['member_logo'] = passport_avatar_show($member_id, 'middle', TRUE);
                        $row['account'] = isset($member_info->account) ? $member_info->account : '';
                        $follow_list[$member_id] = $row;
                    }
                    unset($fans);
                }
                
                //我关注的
                if(!empty($fans_id))
                {
                    $follows = $this->get_my_follow($member['member_id'], $fans_id, false);
                    if(!empty($follows))
                    {
                        foreach ($follows as $row)
                        {
                            if(isset($follow_list[$row['b_id']]) && ($row['status'] == Liaoqiu_friendship_model::STATUS_NORMAL))
                            {
                                $follow_list[$row['b_id']]['follow'] = Liaoqiu_friendship_model::FOLLOW_EACH;
                            }
                        }
                    }
                }
                
                
                $return['state_code'] = 0;
                $return['state_desc'] = '成功';
                $return['fans_list'] = array_values($follow_list);
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
        }
        
  
        /**
         * 关注好友列表
         * 
         * @param string $token
         */
        public function attention_friend($token, $is_friend = 0)
        {
            
            $token = urldecode($token);
            $return = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try
            {
                if(empty($token))
                {
                    throw new Exception('参数错误', 18010);
                }
                
                $member = getuser_by_token($token);
                load_model('friendship');
                $friend = $friends_id = $friends_list = array();
                //好友
                $friend = $this->liaoqiu_friendship_model->getRecordsByAID($member['member_id']);
                
                if(!empty($friend))
                {
                    $tmp_friend = array();
                    foreach ($friend as $row)
                    {
                        $tmp_friend[] = $row;
                    }
                    
                    $blacklist = array();
                    //是好友列表，则必须去除黑名单列表数据
                    if(!empty($is_friend))
                    {
                        load_model('blacklist');
                        $blacklist = $this->liaoqiu_blacklist_model->getRecordsByAID($member['member_id']);
                        
                    }
                    
                    if(!empty($blacklist))
                    {
                        foreach ($blacklist as $row)
                        {
                            if(isset($tmp_friend[$row['a_id']]))
                            {
                                unset($tmp_friend[$row['a_id']]);
                            }
                            
                            if(isset($tmp_friend[$row['b_id']]))
                            {
                                unset($tmp_friend[$row['b_id']]);
                            }
                        }
                    }
                    
                    $friend = $tmp_friend;
                    $user_ids = array();
                    foreach ($friend as $row)
                    {
                        $user_ids[] = $row['b_id'];
                    }
                    
                    
                    
                    $users = getinfo_byuserid($user_ids);
                    if($users['state_code'] != 0)
                    {
                        throw new Exception($users['state_desc'], $users['state_code']);
                    }
                    
                    $friend = add_account_hx_username($users['user_detail']);
                    load_model('member');
                    foreach ($friend as $row)
                    {
                        $member_id = $row['member_id'];
                        $friends_id[] = $member_id;
                        //$row['member_logo'] = passport_avatar_show($member_id, 'middle', TRUE);
                        $row['follow'] = Liaoqiu_friendship_model::FOLLOW_DELETE;
                        $friends_list[$member_id] = $row;
                    }
                }
                
                //我关注的好友
                if(!empty($friends_id))
                {
                    $follows = $this->get_my_follow($member['member_id'], $friends_id, FALSE);
                    if(!empty($follows))
                    {
                        foreach ($follows as $row)
                        {
                            if(isset($friends_list[$row['b_id']]) && ($row['status'] == Liaoqiu_friendship_model::STATUS_NORMAL))
                            {
                                $friends_list[$row['b_id']]['follow'] = Liaoqiu_friendship_model::FOLLOW_NORMAL;
                            }
                        }
                    }
                    //关注我的好友
                    $follows = $this->get_follow_my($friends_id, $member['member_id'], FALSE);
                    if(!empty($follows))
                    {
                        foreach ($follows as $row)
                        {
                            if(!isset($friends_list[$row['a_id']]) || $row['status'] != Liaoqiu_friendship_model::STATUS_NORMAL)
                            {
                                continue;
                            }
                            $friends_list[$row['a_id']]['follow']  = $friends_list[$row['a_id']]['follow'] == Liaoqiu_friendship_model::FOLLOW_NORMAL ? Liaoqiu_friendship_model::FOLLOW_EACH : Liaoqiu_friendship_model::FOLLOW_ME;

                        }
                    }
                }
                
                
                if(!empty($is_friend))
                {

                    $result = array();
                    $this->load->library('py_class');
                    $py=new py_class();
                    foreach ($friends_list as $row)
                    {
                        $first_word = '#';
                        $nick_name = trim($row['nick_name']) ;
                        
                        //取首字母
                        if(!empty($nick_name))
                        {
                            $first_word = substr($py->str2py($nick_name), 0, 1);
                            $first_word = strtoupper($first_word);
                        }
                        
                        $first_word = preg_replace('/[^A-Z]/i', '#', $first_word);
                        $result[$first_word][] = $row;
                    }
                    asort($result);
                    $friends_list = $result;
                }
                else 
                {
                    $friends_list = array_values($friends_list);
                }
                
                $return['state_code'] = 0;
                $return['state_desc'] = '成功';
                $return['friends_list'] = $friends_list;
                //$return['black_list'] = $blacklist;
                //$return['friend'] = $friend;
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
        }
        
        /**
         * 关注/取消关注操作
         * 
         */
        public function follow($token, $b_id, $op)
        {
            
            $token = urldecode($token);
            $b_id = urldecode($b_id);
            $op = urldecode($op);
            $return = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try
            {
                
                if(empty($token) || empty($b_id) || !isset($op) )
                {
                    throw new Exception('参数错误', 19010);
                }
                //1:关注 0：取消关注
                $op_arr = array(0, 1);
                $tmp_op = intval($op);
                if((strval($tmp_op) != $op) || !in_array($op, $op_arr))
                {
                    throw new Exception('参数错误', 19011);
                }
                
                $member = getuser_by_token($token);
                
                load_model('friendship');
                $follow = $this->liaoqiu_friendship_model->getRecordsByBID($member['member_id'], $b_id);
                
                if(empty($follow) && !empty($op))
                {
                    load_model('blacklist');
                    $blacklist = $this->liaoqiu_blacklist_model->getRecordByAIDBID($member['member_id'], $b_id);
                    if(!empty($blacklist))
                    {
                        foreach ($blacklist as $row)
                        {
                            if($row['status'] == 1)
                            {
                                _E('你已被拉黑', 190132);
                            }
                        }
                    }
                    $friendship = $this->liaoqiu_friendship_model->getRecordByAIDBID($member['member_id'], $b_id);
                    if(!empty($friendship) && ($friendship['status'] == 1))
                    {
                        _E('你已经关注过对方', 190156);
                    }
                    
                    $params = array();
                    $params['a_id'] = $member['member_id'];
                    $params['b_id'] = str_replace('5usport_', '', $b_id);
                    $params['status'] = Liaoqiu_friendship_model::STATUS_NORMAL;
                    $params['add_time'] = time();
                    $result = $this->liaoqiu_friendship_model->setRecord($params);
                    
                    if(empty($result))
                    {
                        throw new Exception('关注失败', 190132);
                    }
                }
                elseif (!empty ($follow)) 
                {
                    $follow = array_pop($follow);
                    $params = array();
                    $params['status'] = $op == 1 ? Liaoqiu_friendship_model::STATUS_NORMAL : Liaoqiu_friendship_model::STATUS_DELETE;
                    $result = $this->liaoqiu_friendship_model->editByID($follow['id'] , $params);
                    
                    if(empty($result))
                    {
                        throw new Exception('操作失败', 190125);
                    }
                }
                
                $return['state_code'] = 0;
                $return['state_desc'] = $op == 1 ? "关注成功" : '取消关注成功';
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
        }


    //获取用户的详细信息
    public function getuserdetail() {
        $token = $this->input->get('token', true);
        $member_id = $this->input->get('member_id', true);
        
        //判断参数完整性
        if(!isset($token) && !isset($member_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }
        //先校验令牌token, 并获取用户信息
        if(!empty($token))
        {
            $return_lq_member = getuser_by_token($token);
        }
        else
        {
            $return_lq_member['member_id'] = FALSE;
        }
        
        //die($member_id);
        $arr_user_list = getinfo_byuserid($member_id);
        $arr_role = $this->getrolebyuserid($member_id);
        //echo "<pre>"; print_R($arr_memberinfo_return); echo "</pre>";
        if($arr_user_list["state_code"]!="0") {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $arr_user_list["state_desc"];
            output($this->res);
        }
        $arr_user_list = $arr_user_list["user_detail"];
        $arr_user_list = add_account_hx_username($arr_user_list);
        //echo "<pre>"; print_r($arr_user_list);echo "</pre>";
        //echo json_encode($arr_user_list);
        
        
        foreach ($arr_user_list as $k => $v) {
            //Do something here.
            $v["userid"] = isset($v["member_id"]) ? $v["member_id"] : '';
            $v["role"] = isset($arr_role[$v["member_id"]] ) ? $arr_role[$v["member_id"]]["role"] : $this->liaoqiu_member_model->getDefaultRole();
            $v['member_logo'] = isset($arr_role[$v["member_id"]] ) ? $arr_role[$v["member_id"]]["member_logo"] : $v['member_logo'];
            
           //粉丝数
            $v['fans_count'] = get_fans_count($return_lq_member['member_id']);
            //关注数:
            $v['follows_count'] = get_follows_count($return_lq_member['member_id']);
            //爱队：
            $v['focusteam'] = get_focusteam($return_lq_member['member_id']);
            //相册
            $v['album'] = get_album($return_lq_member['member_id']);
            
            
            //校验用户账号列表合法性
            $token_member_id = $return_lq_member['member_id'];
            $v['follow'] = 0;
            $v['is_black'] = 0;
            if($v['member_id'] != $token_member_id)
            {
                $my_follow = $this->get_my_follow($token_member_id, $v['member_id']);
                if(!empty($my_follow))
                {
                    $v['follow'] = Liaoqiu_friendship_model::FOLLOW_NORMAL;
                }
                
                $follow_my = $this->get_follow_my($v['member_id'], $token_member_id);
                $tmp_follow = 0;
                if(!empty($follow_my))
                {
                    $tmp_follow = Liaoqiu_friendship_model::FOLLOW_ME;
                }
                
                if(!empty($tmp_follow) && ($v['follow'] == Liaoqiu_friendship_model::FOLLOW_NORMAL) )
                {
                    $v['follow'] = Liaoqiu_friendship_model::FOLLOW_EACH;
                }
                //黑名单关系
                //is_black 0:正常 1 我拉黑他 2：他拉黑我 3：互相拉黑
                load_model('blacklist');
                $is_black = $this->liaoqiu_blacklist_model->getRecordByAIDBID($token_member_id, $v['member_id']);
                if(!empty($is_black))
                {
                    $v['is_black'] = 1;//1 我拉黑他 
                }
                
                $he_black = 0;
                $is_black = $this->liaoqiu_blacklist_model->getRecordByAIDBID($v['member_id'], $token_member_id);
                if(!empty($is_black))
                {
                    $he_black = 1;
                }
                                
                if(!empty($he_black))
                {
                    $v['is_black'] = 2;//2：他拉黑我
                    
                    if(!empty($v['is_black']))
                    {
                        $v['is_black'] = 3;//互相拉黑
                    }
                }
                
            }
            
            
            $arr_result[] = lq_member_init($v);
        }
        
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["user_list"] = $arr_result;
        output($this->res);
    }
    
        
        public function upload_file()
        {
            
            $type = $this->input->post('type', true);
            $type_arr = array('show', 'photo', 'head');
            $return = array('state_code' => 0, 'state_desc' => '成功');
            try 
            {

                //$type = 'show';
                if(!in_array($type, $type_arr))
                {
                    throw new Exception('类型不支持', 300103);
                }    
                
                
                //上传
                $dir_arr = array('show' => 'show', 'photo' => 'photo', 'head' => 'head');
                switch ($type)
                {
                    case 'head':
                        
                        break;
                }
                
                $return = upload_file($dir_arr[$type]);
                if(empty($return['file_url']))
                {
                    output($return);
                }

                switch ($type)
                {
                    case 'photo':
                        $picname = $_FILES['file']['name'];
                        $result = $this->upload_photo(ltrim($return['file_path'], '.'), $picname);
                        if(empty($result))
                        {
                            _E('保存失败', 400100);
                        }
                        
                        load_model('member_photo');
                        $data = $this->liaoqiu_member_photo_model->getRecord($result);
                        $return = array_merge($return, $data);
                        unset($return['real_type'], $return['desc']);
                        break;
                    
                    case 'head' :
                        $token = $this->input->post('token', true);
                        $return_member = getuser_by_token($token);
                        $userid = $return_member['member_id'];
                        $tmp_file = $return['file_path'];
                        $type = trim($return['real_type'], '.');
                        
                        $result = new_passport_avatar_create($userid, $tmp_file, $type);
                        if($result !== TRUE)
                        {
                            throw new Exception('操作失败|' . $result, 13040);
                        }
                        unset($return['real_type']);
                        //unlink($tmp_file);
                        $logo_url = passport_avatar_show($userid, 'middle', TRUE);
                        
                        $data = array(
                            'member_logo' => $logo_url,
                        );
                        $this->liaoqiu_member_model->editMemberByMemberID($userid, $data);
                        $return['file_url'] = $logo_url;
                        break;
                    
                    case 'show':
                        
                    default :
                        break;
                }
                
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            $return['state_code'] = 0;
            $return['state_desc'] = '上传成功';
            unset($return['file_path']);
            output($return);
            //return $return;
        }
        
        /**
         * 相册点赞
         */
        public function photo_zan()
        {
            $token = $this->input->post('token', true);
            $p_id = $this->input->post('p_id', true);
            $status = $this->input->post('status', true);
            
            $return = array(
                 "state_code"    =>    0,
                "state_desc"    =>    "提交成功",
            );
            try
            {
             
                if(empty($token) || empty($p_id) || empty($status))
                {
                    _E('参数错误', 100);
                }   
                
                if(!in_array($status, array(1, 2)))
                {
                    _E('点赞状态错误', 25013);
                }
                
                $return_lq_member = getuser_by_token($token);
                load_model('member_photo');
                $photo = $this->liaoqiu_member_photo_model->getRecord($p_id);
                
                if(empty($photo))
                {
                    _E('图片不存在', 25014);
                }
                
                $data = array(
                    'p_id' => $p_id,
                    'member_id' => $return_lq_member['member_id'],
                    'status' => $status,
                );
                load_model('photo_zan');
                $result = $this->liaoqiu_photo_zan_model->setRecord($data);
                
                if(empty($result))
                {
                    _E('保存失败', 25015);
                }
                
            } 
            catch (Exception $ex) 
            {

                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
                
            }
            output($return);
            
        }
        
        
        
    /**
     * 获取用户的的相册
     * 
     * @throws Exception
     */
    public function list_photo()
    {
        $return = array('state_code' => -99, 'state_desc' => '非法提交');
        $member_id = $this->input->get('member_id', true);
        $token = $this->input->get('token', true);
        
           try
            {
                if(empty($member_id))
                {
                    throw new Exception('参数错误', 15020);
                }
                
                $user =  false;
                if(!empty($token))
                {
                    $user = getuser_by_token($token);
                }
                
                load_model('member');
                $member = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
                if(empty($member) || Liaoqiu_member_model::STATUS_USABLE != $member['status'] )
                {
                    throw new Exception('参数错误', 15021);
                }
                
                load_model('member_photo');
                $list = $this->liaoqiu_member_photo_model->getRecordsByMemberID($member_id);
                if(!empty($list))
                {
                    if(!empty($user))
                    {

                        $pids = array();
                        foreach ($list as $r)
                        {
                            $pids[] = $r['id'];
                        }

                        load_model('photo_zan');
                        $zan = $this->liaoqiu_photo_zan_model->getRecordByPId($pids, $user['member_id']);
                        $zan_list = array();
                        if(!empty($zan))
                        {
                            foreach ($zan as $z)
                            {
                                $zan_list[$z['p_id']] = true;
                            }
                        }
                    }
                    
                    foreach ($list as &$row)
                    {
                        $row['file_url'] = get_access_path($row['file_path']);
                        $row['zan'] = 0;
                        
                        if(!empty($user))
                        {
                            
                            if(isset($zan_list[$row['id']]) )
                            {
                                $row['zan'] = 1;
                            }
                        }
                    }
                }
                
                $return['state_code'] = 0;
                $return['state_desc'] = "成功";
                $return['photo_list'] = $list;
            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }
            
            output($return);
    }
    
    public function test()
    {
        

//        $member_id = 4151;
//        $member_id = $_GET['member_id'];
//        //签名内容
//        $sig_fie_content = qcloud_sig($member_id);
//        print_r($sig_fie_content);
//        echo "<hr />";
//        //验证签名
//        $array_qcloud = array(
//            "account_type" => get_config_field('qcloud_account_type'),
//            "identifier" => $member_id,
//            "appid_at_3rd" => get_config_field('qcloud_appid_at_3rd'),
//            "sdk_appid" => get_config_field('qcloud_sdk_appid'),
//            "expire_after" => get_config_field('qcloud_expire_after'),    //一个月
//        );
//        $install_path = get_config_field('qcloud_install_path');
//        $pub_file =  '/data/www/liaoqiu/upload/certs/public.pem';
//        $sig_file = '/data/www/liaoqiu/upload/qcloud/user_sig/' . $member_id . '_sig';
//        
//        $str_command = $install_path . "/tls_licence_tools 2 ". $pub_file." " .$sig_file . " "  .$array_qcloud["sdk_appid"]." ".$array_qcloud["account_type"]." ".$array_qcloud["appid_at_3rd"] . " ".$array_qcloud["identifier"];
//        $ret = exec($str_command, $output, $return_var);
//        
//        print_r($output);
//        var_dump($return_var);
//        
//        if($return_var == -1)
//        {
//            echo '失败';
//        }
//        else
//        {
//            echo 'OK';
//        }
    exit;
        
        
    }
}
?>