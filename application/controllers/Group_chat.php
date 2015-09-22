<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 群聊功能
 * 
 */
class Group_chat extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "非法提交",
    );

    /**
     * 默认群名称
     * 
     * @var string
     */
    public $default_group_name = '聊天信息';
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        
        parent::__construct();
        load_model('group');
        $this->load->helper('huanxin_helper');
        verify_sign();
    }
    
    /**
     * 测试
     * 
    */
    public function test()
    {

        echo '测试';
    }

        
    /**
     * 创建群
     * 
     */
    public function creategroup() 
    {
        $token = $this->input->post('token', true);
        $group_title = $this->input->post('group_title', true);
        $group_desc = $this->input->post('group_desc', true);
        $is_author = $this->input->post('is_author', true);
        $public = $this->input->post('public', true);
        $approval = $this->input->post('approval', true);
        $maxnum = $this->input->post('maxnum', true);
        $group_member = $this->input->post('group_member', true);
        //合成群聊的头像列表
        $head_faces = array();
        
        //判断参数完整性
        if(!isset($token)  || !isset($group_member)) 
        {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            $this->format_output($this->res);
        }

        $member = getuser_by_token($token);
        $member_id = $member['member_id'];
        $head_faces[] = $member['member_logo'];
        
        //群名称
        if(empty($group_title) || countstring($group_title) < 2 || countstring($group_title) > 30) 
        {
            $group_arr = $this->liaoqiu_group_model->getRecordsByUserID($member_id);
            $count = count($group_arr) + 1; 
            $group_title = $this->default_group_name . '(' . $count . ')';
        }
        
        if(empty($group_desc) || countstring($group_desc) < 10 || countstring($group_desc) > 100) 
        {
            if(!isset($count) || empty($count))
            {
                $group_arr = $this->liaoqiu_group_model->getRecordsByUserID($member_id);
                $count = count($group_arr) + 1; 
            }
            $group_desc = $this->default_group_name . '(' . $count . ')';
        }
        
        if(!in_array($public, array(0, 1)) ) 
        {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "public不对";
            output($this->res);
        }
        
        if(!in_array($approval, array(0, 1)) ) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "approval不对";
            output($this->res);
        }
        
        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);
        //echo "<pre>"; print_R($return_lq_member);
        $aid = $return_lq_member["member_id"];
            
        $is_author = 0;
        $maxnum = 499;
        $arr_group_member_new = array();
        $arr_group_member_id_new = array();
        
        
        if($group_member != "NULL") {
            $arr_group_member = array();

            //校验用户账号列表合法性
            $member_id_list = deal_many_hxaccount($group_member);
            $arr_group_member = explode(",", $member_id_list);

            //手机客户端 普通用户 拉人进群，前提是这个人是你的好友。
            //判断这些ID是否是你的好友
            $aid = $return_lq_member["member_id"];

            load_model('friendship');
            load_model('member');
            foreach($arr_group_member as $k => $v) 
            {
                $friendship = $this->liaoqiu_friendship_model->getRecordByAIDBID($aid, $v);
                //$arr_group_member_new[] = $return_lq_member["id"];
                if($friendship["status"]="1") 
                {
                    $arr_group_member_new[] = get_huanxin_account($v);
                    $arr_group_member_id_new[] = $v;
                } 
                else 
                {
                    //echo $v."不是你好友";
                    $this->res["state_code"] = -2;
                    if($is_author == "1") {
                        $this->res["state_desc"] = "用户 5usport_".$v."已经被冻结，不能拉进群";
                    } else {
                        $this->res["state_desc"] = "非法操作，不能邀请非自己好友的用户进群";
                    }
                    output($this->res);
                }
            }
        }
        else 
        {
            output($this->res);
        }

        if(empty($arr_group_member_id_new))
        {
            output($this->res);
        }
        //所拉的用户列表，不能超过群本身的最大容纳人数，等于也不行，因为包括群主在内的
        if(count($arr_group_member_id_new) >= $maxnum) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "你建的是".$maxnum."人的群，但是你拉的用户（包含群主）已经超过了这个数";
            output($this->res);
        }

        if($is_author == "1" && count($arr_group_member_id_new) > 1) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "目前最多只支持两个主播";
            output($this->res);
        }

        $approval = 1 ? $approval == "1" : false;
        $public = 1 ? $public == "1" : false;
        $hx_owner = "5usport_".$aid;
        $u5_owner = $return_lq_member["account"];

        //echo "<pre>"; print_R($return_lq_member);die;
        //$arr_group_member_new = array("libo", "polly");

        $hx_action = "newgroup";
        $tmp_params = array();
        $tmp_params["groupname"] = $group_title;                    //聊天室名称
        $tmp_params["desc"] = $group_desc;                          //聊天室简介
        $tmp_params["public"] = $public;                            //是否为公开群true or false
        $tmp_params["maxusers"] = 0 + $maxnum;                      //群人数限制（默认为200）
        $tmp_params["approval"] = $approval;                        //是否需要审核
        $tmp_params["owner"] = $hx_owner;                           //群管理员是谁？

        
        if(count($arr_group_member_new)>0) {
            $tmp_params["members"] = $arr_group_member_new;        //拉好友进群
        } else {
            //$tmp_params["members"] = "";
        }
        //echo "<pre>"; print_R($tmp_params);print_R($huanxin_result);
        $huanxin_result = huanxin($hx_action, $tmp_params);
        //write_logs(1, $token, "环信创建群的返回结果", var_export($huanxin_result, true));
        $reason = "服务器繁忙，请稍候重试";

        huanxin_result($huanxin_result, $hx_action);

        if(!array_key_exists("groupid", $huanxin_result["huanxin_result"]["data"])) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $reason."4";
            output($this->res);
        }

        $hx_groupid = $huanxin_result["huanxin_result"]["data"]["groupid"];

        //环信建群成功后,再在数据库建群
        $add_time = time();
        $arr_params = array();
        $arr_params["hx_groupid"] = $hx_groupid;
        $arr_params["owner"] = $u5_owner;
        $arr_params["member_id"] = $aid;
        $arr_params["title"] = $group_title;
        $arr_params["desc"] = $group_desc;
        $arr_params["is_author"] = $is_author;
        $arr_params["is_public"] = $public;
        $arr_params["is_approval"] = $approval;
        $arr_params["maxnum"] = $maxnum;
        $arr_params["add_time"] = $add_time;
        $arr_params["status"] = 1;
		

        $group_id = $this->liaoqiu_group_model->setRecord($arr_params);

        //写入群记录表
        
        if(empty($group_id )) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "创建聊天室失败";
            output($this->res);
        }
        
        load_model('member');
        $arr_group_member_id_new = array_unique($arr_group_member_id_new);
        foreach ($arr_group_member_id_new as $k => $v)
        {
            $member_info = $this->liaoqiu_member_model->getMemberByMemberID($v);
            if(!empty($member_info))
            {
                $head_faces[] = $member_info['member_logo'];
            }
            
        }
        
        $head_faces = array_slice($head_faces, 0, 3);
        
        //修改群组头像
        $group_logo = create_group_face($head_faces, $group_id);
        if($group_logo !== false)
        {
            $group_logo = 'http://' . $_SERVER['HTTP_HOST']   . str_replace('\\', '/', ltrim($group_logo, '.'));
            $arr_group_params = array();
            $arr_group_params['group_logo'] = $arr_params['group_logo'] = $group_logo;
            $this->liaoqiu_group_model->editRecordByID($group_id, $arr_group_params);
        }
        
        /*
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `member_id` int(9) NOT NULL COMMENT '5u userid',
        `group_id` int(9) NOT NULL COMMENT '群ID',
        `is_owner` int(1) NOT NULL COMMENT '是否是群主, 1为是，0为否',
        `logs_time` int(10) NOT NULL COMMENT '日志时间',
        `type` int(1) NOT NULL COMMENT '日志类型，1为已经加群，2为已被踢出群，0为待确认，3为拒绝加入，4已经被永久拒绝',
        */
        $arr_logs_params = array();
        $arr_logs_params["member_id"] = $aid;
        $arr_logs_params["group_id"] = $group_id;
        $arr_logs_params["is_owner"] = 1;
        $arr_logs_params["logs_time"] = $add_time;
        $arr_logs_params["type"] = 1;

        //先把群主拉进来
        load_model('group_members');
        $model_result = $this->liaoqiu_group_members_model->setRecord($arr_logs_params);

        //如果有组员，则循环把组员拉进来
        
        if(count($arr_group_member_id_new)>0){
            foreach($arr_group_member_id_new as $k => $v) {
                                
                $arr_logs_params["member_id"] = $v;
                $arr_logs_params["is_owner"] = 0;
                $arr_logs_params['type'] = 1;
                $model_result = $this->liaoqiu_group_members_model->insertRecord($arr_logs_params);
                
            }
        }

        $arr_params["id"] = $group_id;

        foreach ($arr_params as $k => $v) {
            //Do something here.
            if($v===false){
                $arr_params[$k] = "0";
            }
            if($v===true){
                $arr_params[$k] = "1";
            }
        }

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "创建成功";
        //$this->res["group_id"] = $group_id;
        //$this->res["hx_group_id"] = $hx_groupid;
        $this->res["group_detail"] = $arr_params;
        output($this->res);
    }
    
    /**
     * 格式化输出数据
     * 
     * @param mixed $data 数据
     * @param string $type 类型
     */
    public function format_output($data, $type = 'json')
    {
        switch ($type)
        {
            case 'json':
                output($data);
                break;
            
            default :
                exit($data);
                
        }
    }
    
    
    
    //获取用户自建群组列表
    public function getgrouplist() {
        $token = $this->input->get('token', true);
        $type = $this->input->get('type', true);
        
        //判断参数完整性
        if(!isset($token) || !isset($token)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //type为0表示查询自己建的群，为1表示自己加入的群
        if($type!="1" && $type!="0") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "type错误";
            output($this->res);
        }


        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);

        $member_id = $return_lq_member["member_id"];
        
        if($type=="1") {
            //获取所有群
            //$group_list = $this->liaoqiu_group_model->getAllAvalableRecordsByUserID($member_id);
            //print_R($group_list);die;
        } else if($type=="0") {
            //获取自己创建的群
            //$group_list = $this->liaoqiu_group_model->getAvalableRecordsByUserID($member_id);
        }
        load_model('group_members');
        $group_member_list = $this->liaoqiu_group_members_model->getAvailableRecordByMemberID($member_id);
        
        $arr_group_id = array();
        if(!empty($group_member_list))
        {
            foreach ($group_member_list as $row)
            {
                if(!isset($arr_group_id[$row['group_id']]))
                {
                    
                    $arr_group_id[$row['group_id']] = $row['group_id'];
                }
                
            }
        }
        
        $group_list = $this->liaoqiu_group_model->getRecordByManyID($arr_group_id, 1);
        
        //echo "<pre>"; print_r($group_list);echo "</pre>";
        $arr_group_list = array();
        foreach ($group_list as $k => $v) {
            $arr_group_list[$k] = $v;
            $arr_group_list[$k]["hx_username"] = "5usport_".$v["member_id"];
            
        }
        //echo "<pre>"; print_r($arr_group_list);echo "</pre>";

        //此处模拟输出版本信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["group_list"] = $arr_group_list;
        output($this->res);
    }
    
    
    
    //修改聊天室资料
    public function modifygroup() {
        $token = $this->input->post('token', true);
        $group_id = $this->input->post('group_id', true);
        $group_title = $this->input->post('group_title', true);
        $group_desc = $this->input->post('group_desc', true);
        $maxnum = $this->input->post('maxnum', true);
        

        //判断参数完整性
        if(!isset($token) || !isset($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if(!is_num($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "group_id错误";
            output($this->res);
        }

        $arr_params = array();
        if(!empty($group_title) ) {
            if(countstring($group_title) < 3 || countstring($group_title) > 40) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "group_title不对".countstring($group_title);
                output($this->res);
            }
            $arr_params["group_title"] = $group_title;
        }
        
        if(!empty($group_desc) ) {
            if(countstring($group_desc) < 20 || countstring($group_desc) > 100) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "group_desc不对";
                output($this->res);
            }
            $arr_params["group_desc"] = $group_desc;
        } 
        
        if(!empty($maxnum) ) {
            if(!is_num($maxnum)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "maxnum错误";
                output($this->res);
            }
            $arr_params["maxnum"] = $maxnum;
        }

        if(count($arr_params)==0) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "没有传入修改项";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);

        $member_id = $return_lq_member["member_id"];
        $where = array('id' => $group_id, "member_id"=> $member_id, "status"=> "1");
        $group_detail = $this->liaoqiu_group_model->getRecordByArray($where);
        $hx_groupid = $group_detail["hx_groupid"];
        $is_author = $group_detail["is_author"];

        if(count($group_detail)==0) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "你不是群主，不能修改群资料";
            output($this->res);
        }

        //群人数限制
        if($is_author == "0" && $maxnum > 500) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "用户自建群人数不能超过500";
            output($this->res);
        }
        if($is_author == "1" && $maxnum > 2000) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "群人数已经超过上限";
            output($this->res);
        }

        //如果要修改群最大容纳人数，则需要读取当前群有几个人。两种方案，一种是取我们的数据库数据，一种是取环信群成员列表。我取了第二种

        //第一种方案： 我先不用这种，我用环信的
        /*
        $this->load->model('liaogeqiu/liaogeqiu_grouplogs_model');
        $where = array('id' => $group_id, "type"=> "1");
        $group_detail = object_array($this->liaogeqiu_grouplogs_model->getRecordCount($where));
        */

        //第二种方案： 查环信群的成员列表
        $hx_action = "getgroupusers";
        $tmp_params = array();
        $tmp_params["group_id"] = $hx_groupid;                                    //聊天室名称
        $huanxin_result = huanxin($hx_action, $tmp_params);
        huanxin_result($huanxin_result, $hx_action);

        if(!empty($maxnum) && (count($huanxin_result["huanxin_result"]["data"]) > $maxnum) ) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "你群成员已经比你要设置的群人数大了，请把maxnum改大一些";
            output($this->res);
        }

        //先修改环信
        $hx_action = "modifygroup";
        $tmp_params = array();
        $tmp_params["group_id"] = $hx_groupid;                                    //聊天室ID
        $tmp_params["groupname"] = $group_title;                                //聊天室标题
        
        if(!empty($group_desc))
        {
            $tmp_params["description"] = $group_desc;                            //群详情e
           
        }
        
        if(!empty($maxnum))
        {
         
            $tmp_params["maxusers"] = 0 + $maxnum;                                //群人数限制（默认为200）
           
        }
        
        $huanxin_result = huanxin($hx_action, $tmp_params);
        //echo "<pre>"; print_R($tmp_params);print_R($huanxin_result);
        //die;
        write_logs(1, $token, "环信创建群的返回结果", var_export($huanxin_result, true));

        huanxin_result($huanxin_result, $hx_action);

        $reason = "服务器繁忙，请稍候重试";

        if(count($huanxin_result["huanxin_result"]["data"]) != count($arr_params)) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $reason."5";
            output($this->res);
        }

        //再修改5U
        $where = array();
        if(!empty($arr_params['group_title']))
        {
            $where['title'] = $arr_params['group_title'];
        } 
        
        $group_detail = $this->liaoqiu_group_model->editRecordByID($group_id, $where);
        //echo "<pre>"; print_R($group_detail);

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";

        output($this->res);
    }
    
    
    
    
    //获取聊天室资料
    public function getgroupdetail() {

        $token = $this->input->get('token', true);
        $group_id = $this->input->get('group_id', true);
        
        //判断参数完整性
        if(!isset($token) || !isset($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $arr_group_id = array_filter(explode(",", $group_id) );
        $arr_group_id_new = array();
        foreach($arr_group_id as $k => $v) {
            if(!is_num($v)) {
                    $this->res["state_code"] = -99;
                    $this->res["state_desc"] = "group_id错误, 应是纯数字";
                    output($this->res);
                } else {
                    $arr_group_id_new[] = $v;
                }
                
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);
        $group_list = $this->liaoqiu_group_model->getRecordByManyID($arr_group_id_new);

        $arr_group_list = array();
        foreach ( $group_list as $k => $v) {
            $v["hx_username"] = "5usport_".$v["member_id"];
            $arr_group_list[$k] = $v;
        }
        //此处模拟输出版本信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["group_detail"] = $arr_group_list;
        output($this->res);
    }
    
    
    //拉好友进群
    public function invitefriendtogroup() {
        $token = $this->input->post('token', true);
        $group_id = $this->input->post('group_id', true);
        $group_member = $this->input->post('group_member', true);
        
        //判断参数完整性
        if(!isset($token) || !isset($group_id) || !isset($group_member) || empty($group_member)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);

        $member_id = $return_lq_member["member_id"];

        $arr_group_member_new = array();
        $arr_group_member_id_new = array();
        if(!empty($group_member )) {
            $arr_group_member = array();

            //校验用户账号列表合法性
            $member_id_list = deal_many_hxaccount($group_member);
            $arr_group_member = explode(",", $member_id_list);

            //手机客户端 普通用户 拉人进群，前提是这个人是你的好友。
            //判断这些ID是否是你的好友
            $aid = $return_lq_member["member_id"];

            load_model('friendship');
            load_model('member');
            foreach($arr_group_member as $k => $v) {
                 //如果不是主播，必须是好友
                 $friendship = $this->liaoqiu_friendship_model->getRecordByAIDBID($aid, $v);
                //$arr_group_member_new[] = $return_lq_member["id"];
                if($friendship["status"]=="1") {
                    $arr_group_member_new[] = "5usport_".$v;
                    $arr_group_member_id_new[] = $v;
                } else {
                    //echo $v."不是你好友";
                    $this->res["state_code"] = -2;
                    $this->res["state_desc"] = "非法操作，不能邀请非自己好友的用户进群";
                    //output($this->res);
                }
            }
        }

        //获取群资料
        $group_detail = $this->liaoqiu_group_model->getRecordByID($group_id);
        $hx_groupid = $group_detail["hx_groupid"];

        if(count($group_detail)==0) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "不存在此群，或者群已被删除";
            output($this->res);
        }

        if($group_detail["status"]!="1") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群已不存在";
            output($this->res);
        }


        $maxnum = $group_detail["maxnum"];

        load_model('group_members');
        $group_list =  $this->liaoqiu_group_members_model->getRecordsByGroupID($group_id) ;
        $group_member_num = count($group_list);

        $arr_filter_member = array();
        //这里过滤掉已经存在的用户
        foreach($arr_group_member_id_new as $k => $v) {
            foreach($group_list as $k1 => $v1) {
                if($v==$v1["member_id"]) {
                    $arr_filter_member[] = $v;
                    unset($arr_group_member_id_new[$k]);
                    unset($arr_group_member_new[$k]);
                }
            }
        }
        //echo "<pre>"; print_R($arr_group_member_id_new);die;

        $remain_num = $maxnum - $group_member_num;

        //所拉的用户列表，不能超过群本身的最大容纳人数，等于也不行，因为包括群主在内的
        if(count($arr_group_member_id_new) >= $remain_num) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "你最多还能添加".$remain_num."个成员";
            output($this->res);
        }



        //先去环信拉人
        $hx_action = "batchaddtogroup";
        $tmp_params = array();
        $tmp_params["group_id"] = $hx_groupid;                                                //聊天室ID
        $tmp_params["user"] = $arr_group_member_new;                                //成员数组
        $huanxin_result = huanxin($hx_action, $tmp_params);
        //echo "<pre>"; print_R($tmp_params);print_R($huanxin_result);
        //die;
        write_logs(1, $token, "环信创建群的返回结果", var_export($huanxin_result, true));

        huanxin_result($huanxin_result, $hx_action);


        $reason = "服务器繁忙，请稍候重试";

        if(count($huanxin_result["huanxin_result"]["data"]["newmembers"]) != count($arr_group_member_new)) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $reason."5";
            output($this->res);
        }


        //后再在5U写记录
        $add_time = time();
        $arr_params = array();
      $arr_params["group_id"] = $group_id;
      $arr_params["is_owner"] = 0;
      $arr_params["logs_time"] = $add_time;
      $arr_params["type"] = 1;

        //循环把组员拉进来
        if(count($arr_group_member_id_new)>0) {
            foreach($arr_group_member_id_new as $k => $v) {
                $arr_params["member_id"] = $v;
                $model_result = $this->liaoqiu_group_members_model->insertRecord($arr_params);
            }
        }

        $str_add_desc = "";
        if(count($arr_filter_member)>0) {
            $str_add_desc = "；有部分用户已经存在在群里，被系统过滤掉了";
        }

        //此处模拟成功的信息
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "拉人成功".$str_add_desc;

        output($this->res);
    }
    
    
    
    
    //获取群成员列表
    public function getgroupmemberlist() {
        
        $token = $this->input->get('token', true);
        $group_id = $this->input->get('group_id', true);
        //判断参数完整性
        if(!isset($token) || !isset($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if(!is_num($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "group_id错误";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);
        $member_id = $return_lq_member["member_id"];

        $group_detail = $this->liaoqiu_group_model->getRecordByID($group_id);
        $is_public = $group_detail["is_public"];
        $is_approval = $group_detail["is_approval"];
        $is_author = $group_detail["is_author"];
        $status = $group_detail["status"];
        $hx_groupid = $group_detail["hx_groupid"];

        if($status == "0") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群已经不存在";
            output($this->res);
        }
        
        load_model('group_members');
        $records_list = $this->liaoqiu_group_members_model->getRecordsByGroupID($group_id);

        if(count($records_list) == 0) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群空无一人";
            output($this->res);
        }

        foreach($records_list as $k => $v) {
            $arr_user_list[] = $v["member_id"];
        }
        $arr_groupmember_list = getinfo_byuserid($arr_user_list);
        $arr_groupmember_list = $arr_groupmember_list["user_detail"];
        foreach ($arr_groupmember_list as $k => $v) {
            $arr_groupmember_list[$k] = lq_member_init($v);
        }
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["member_list"] = $arr_groupmember_list;
        
        output($this->res);
    }

    
    
    
    //删除群
    public function deletegroup() {
        $token = $this->input->post('token', true);
        $group_id = $this->input->post('group_id', true); 
        

        //判断参数完整性
        if(!isset($token) || !isset($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if(!is_num($group_id) || empty($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "group_id错误";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);
        $member_id = $return_lq_member["member_id"];

        
        $group_detail = $this->liaoqiu_group_model->getRecordByID($group_id);
        $is_public = $group_detail["is_public"];
        $is_approval = $group_detail["is_approval"];
        $is_author = $group_detail["is_author"];
        $status = $group_detail["status"];
        $hx_groupid = $group_detail["hx_groupid"];
        $owner = $group_detail["member_id"];

        if($status == "0") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群已经不存在";
            output($this->res);
        }
        if($member_id != $owner) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "你不是群主";
            output($this->res);
        }

        //去环信删除
        $hx_action = "deletegroup";
        $tmp_params = array();
        $tmp_params["group_id"] = $hx_groupid;                                                    //聊天室ID
        $huanxin_result = huanxin($hx_action, $tmp_params);
        //echo "<pre>"; print_R($tmp_params);print_R($huanxin_result);
        //die;
        write_logs(1, $token, "环信创建群的返回结果", var_export($huanxin_result, true));

        huanxin_result($huanxin_result, $hx_action);


        $reason = "服务器繁忙，请稍候重试";

        if($huanxin_result["huanxin_result"]["data"]["success"] != true) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = $reason."5";
            output($this->res);
        }

        //然后在5U删除
        $this->liaoqiu_group_model->deleteRecordByID($group_id);

        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        output($this->res);
    }

    
    
    //删除群成员
    public function deletegroupmember() {
        $token = $this->input->post('token', true);
        $group_id = $this->input->post('group_id', true);
        $hx_account_list = $this->input->post('hx_account_list', true);
        
        //常规判断 ，数据合法性
        //判断群是否还存在，
        //判断此人是否是群主
        //判断username在不在此群中 grouplogs 的 type = 1

        //判断参数完整性
        if(!isset($token) || !isset($group_id) || !isset($hx_account_list)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if(!is_num($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "group_id错误";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $return_lq_member = getuser_by_token($token);
        $member_id = $return_lq_member["member_id"];
        //echo "<pre>"; print_R($return_lq_member);
        
        //删自己
        if(empty($hx_account_list))
        {
            $hx_account_list = $return_lq_member['hx_username'];
        }
        
        //校验用户账号列表合法性
        $all_member_id = deal_many_hxaccount($hx_account_list);

        $group_detail = $this->liaoqiu_group_model->getRecordByID($group_id);
        $is_public = $group_detail["is_public"];
        $is_approval = $group_detail["is_approval"];
        $is_author = $group_detail["is_author"];
        $status = $group_detail["status"];
        $hx_groupid = $group_detail["hx_groupid"];

        if($status == "0") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群已经不存在";
            output($this->res);
        }
        
        load_model('group_members');
        $records_list = $this->liaoqiu_group_members_model->getRecordsByGroupID($group_id);

        $token_is_owner = false;
        $arr_remove_list = explode(",", $all_member_id);

        if(in_array($member_id, $arr_remove_list)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "自己不能踢自己出群";
            output($this->res);
        }

        foreach($records_list as $k => $v) {
            //判断是否群主
            if($v["member_id"]==$member_id && $v["is_owner"]=="1") {
                $token_is_owner = true;
            }
            foreach ($arr_remove_list as $k1 => $v1) {
                //echo $v1;
                if($v1 == $v["member_id"]) {
                    $arr_remove_list_new[] = $v1;
                }
            }
        }

        if(!$token_is_owner) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "你不是群主";
            output($this->res);
        }

        $remove_member_id = implode(",", $arr_remove_list_new);

        if($remove_member_id != $all_member_id) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "指定踢除的人中，有部分用户已经不在此群中";
            output($this->res);
        }

        //调用环信踢人，然后写到5U表（环信只能一个一个踢）所以需要for循环
        $hx_action = "deletegroupuser";
        $failed_delect_user = array();
        $tmp_params = array();
        $tmp_params["group_id"] = $hx_groupid;                                            //聊天室ID
        foreach($arr_remove_list_new as $k => $v) {
            //先去环信踢人
            $tmp_params["user"] = "5usport_".$v;                                //成员数组
            $huanxin_result = huanxin($hx_action, $tmp_params);

            if($huanxin_result["huanxin_result"]["data"]["result"] == true) {
                //写5u数据库，但是需要注意重复的记录
                $add_time = time();
              $arr_params["logs_time"] = $add_time;
                $arr_params["type"] = "2";
                $model_result = $this->liaoqiu_group_members_model->editRecordByMemberAndGroupID($v, $group_id, $arr_params);
            } else {
                $failed_delect_user[] = "5usport_".$v;
            }
        }

        //此处模拟成功的信息
        $this->res["state_code"] = "0";
        $this->res["state_desc"] = "操作成功".var_export($failed_delect_user, true);
        output($this->res);
    }

    
    //退群
    public function exitgroup() {
        $token = $this->input->post('token', true);
        $group_id = $this->input->post('group_id', true);
        
        //常规判断 ，数据合法性
        //判断群是否还存在，
        //找出该群的群主
        //判断username在不在此群中 grouplogs 的 type = 1
        
        //判断参数完整性
        if(!isset($group_id) || !isset($token)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if(!is_num($group_id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "group_id错误";
            output($this->res);
        }

        
        $group_detail = $this->liaoqiu_group_model->getRecordByID($group_id);
        $is_public = $group_detail["is_public"];
        $is_approval = $group_detail["is_approval"];
        $is_author = $group_detail["is_author"];
        $status = $group_detail["status"];
        $hx_groupid = $group_detail["hx_groupid"];

        if($status == "0") {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "此群已经不存在";
            output($this->res);
        }
        
        load_model('group_members');
        $records_list = $this->liaoqiu_group_members_model->getRecordsByGroupID($group_id);
        $member = getuser_by_token($token);
        $member_id = $member['member_id'];
		$group_owner_id = 0;
        $group_members = array();
        foreach($records_list as $k => $v) {
            if($member_id == $v['member_id'])
            {
                
                //判断是否群主，如果是群主，则这个ID不能退群。否则环信会报错
                $group_owner_id = $v["is_owner"]=="1" ? $v["member_id"] : 0;
                $arr_remove_list_new[] = $member_id;
                //break;
            }
            else {
                $group_members[] = $v['member_id'];
            }
        }
	
        if(empty($arr_remove_list_new))
        {
            output($this->res);
        }
        
        //判断是否群主，如果是群主，则这个ID不能退群。否则环信会报错
	if(in_array($group_owner_id, $arr_remove_list_new) && !empty($group_members)) {
                //转让
                $hx_action = 'changeGroupOwner';
                $tmp_params = array();
                $tmp_params['group_id'] = $hx_groupid;
                $newowner_id = array_pop($group_members);
                $tmp_params['newowner'] = '5usport_' . $newowner_id;
                $huanxin_result = huanxin($hx_action, $tmp_params);
                $str_err_add = '';
                if($huanxin_result["huanxin_result"]["data"]['newowner'] == true)
                {
                    //写5u数据库，但是需要注意重复的记录
                    $add_time = time();
                    $arr_params["logs_time"] = $add_time;
                    $arr_params["type"] = "2";
                    //踢人
                    $model_result = $this->liaoqiu_group_members_model->editRecordByMemberAndGroupID($group_owner_id, $group_id, $arr_params);
                    $arr_params = array();
                    $arr_params['is_owner'] = 1;
                    //修改群主
                    $model_result = $this->liaoqiu_group_members_model->editRecordByMemberAndGroupID($newowner_id, $group_id, $arr_params);
                }
                else 
                {
                     $str_err_add = "，有以上用户删除失败：". $tmp_params['newowner'];
                }
        }
        else
        {
            //调用环信踢人，然后写到5U表（环信只能一个一个踢）所以需要for循环
            $hx_action = "deletegroupuser";
            $failed_delect_user = array();
            $tmp_params = array();
            $tmp_params["group_id"] = $hx_groupid;                                            //聊天室ID
            foreach($arr_remove_list_new as $k => $v) {
                //先去环信踢人
                $tmp_params["user"] = "5usport_".$v;                                //成员数组
                $huanxin_result = huanxin($hx_action, $tmp_params);

                if($huanxin_result["huanxin_result"]["data"]["result"] == true) {
                    //写5u数据库，但是需要注意重复的记录
                    $add_time = time();
                                    $arr_params["logs_time"] = $add_time;
                    $arr_params["type"] = "2";
                    $model_result = $this->liaoqiu_group_members_model->editRecordByMemberAndGroupID($v, $group_id, $arr_params);
                } else {
                    $failed_delect_user[] = "5usport_".$v;
                }
            }
                    
            $str_err_add = "";
            
            if(count($failed_delect_user)>0){
            
                $str_err_add = "，有以上用户删除失败：".http_build_query($failed_delect_user);
                
            }
        }
        
        
        //此处模拟成功的信息
        $this->res["state_code"] = "0";
        $this->res["state_desc"] = "操作成功".$str_err_add;
        output($this->res);
    }

}
?>