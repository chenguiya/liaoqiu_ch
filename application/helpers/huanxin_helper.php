<?php

/**
* 提交数据 postCurl
**/
function postCurl ($url, $body, $header = array(), $method = "POST")
{
	if(is_array($body)) {
		$body = http_build_query($body);
	}
	if (count($header)==0) {
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	switch ($method){
		case "GET" :
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		break;
		case "POST":
			curl_setopt($ch, CURLOPT_POST,true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			//echo $body."<BR>";
		break;
		case "PUT" :
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		break;
		case "DELETE":
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		break;
	}

	curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	if (count($header) > 0) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	$ret = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if ($err) {
		return "5U_API_ERROR:".$err."".$url.$body;
	}
	return $ret;
}


function add_account_hx_username($arr_user_list) {
    foreach ($arr_user_list as $k => $v) {
        //Do something here.
        if(@$v["member_logo"]=="")$arr_user_list[$k]["member_logo"] = $v["logo"];
        if(@$v["account"]=="")$arr_user_list[$k]["account"] = $v["username"];
        if(@$v["member_id"]=="")$arr_user_list[$k]["member_id"] = $v["userid"];
        if(@$v["nick_name"]=="")$arr_user_list[$k]["nick_name"] = $v["nickname"];
        if(@$v["hx_username"]=="")$arr_user_list[$k]["hx_username"] = "5usport_".$v["userid"];
        unset($arr_user_list[$k]["logo"], $arr_user_list[$k]["userid"], $arr_user_list[$k]["nickname"], $arr_user_list[$k]["username"]);
    }
    return $arr_user_list;
}


function usport_api($action, $params) {
	//设置各个参数的默认值
	$usport_member_url = get_huanxin_config_field('5u_api_url')."/api/liaoqiu/";
	$url = $usport_member_url.$action;
	$header = array();
	$method = "POST";
	$sign = "";		//签名
	$key = get_huanxin_config_field('5u_api_key');
    foreach($params as $k => $v) {
        $params[$k] = urldecode($v);
    }
	ksort($params);
	$sign_before = urldecode(http_build_query($params))."||".$key;
	//echo $sign_before."<BR>";
	$sign = md5($sign_before);

	$params["sign"] = $sign;
	$str_ret = postCurl ($url, $params, $header, $method);
    //echo $url."?".http_build_query($params)."<BR>";die;
	//echo "5u体育接口返回：".$str_ret."<BR>";
	$res = array();
	if(strpos($str_ret, "5U_API_ERROR:")!==false) {
		$res["state_code"] = -4;
		$res["state_desc"] = $str_ret;
		output($res);
	}
	return $str_ret;
}

function lq_api($request, $params) {
	//设置各个参数的默认值
	$url = get_huanxin_config_field('domain')."/".$request."/api?";
	$header = array();
	$method = "POST";
	$sign = "";		//签名
	$key = get_huanxin_config_field('api_key');

	$sign_before = urlencode($url).$key;
	$sign = md5($sign_before);
    $url .= "sign=".$sign;

	$str_ret = postCurl ($url, $params, $header, $method);
	$res = array();
    if(trim($str_ret)==""){
        $res = array();
        $res["state_code"] = -4;
		$res["state_desc"] = $str_ret;
        $res["url"] = $url;
        return json_encode($res);
    }
	if(strpos($str_ret, "LQ_API_ERROR:")!==false) {
		$res["state_code"] = -4;
		$res["state_desc"] = $str_ret;
		output($res);
	}
    $arr_ret = json_decode($str_ret, true);

    if(is_array($arr_ret)){
        $arr_ret["url"] = $url;
    } else {
        $res = array();
        $res["state_code"] = -4;
		$res["state_desc"] = $str_ret;
        $res["url"] = $url;
        return json_encode($res);
    }
	return json_encode($arr_ret);
}

function add_news_link($article_id) {
	//设置各个参数的默认值
    $domain = get_huanxin_config_field('5u_api_url');
	$key = get_huanxin_config_field('5u_api_key');
    $params = array();
    $params["news_id"] = $article_id;
    $sign_before = urldecode(http_build_query($params))."||".$key;
	//echo $sign_before."<BR>";
	$sign = md5($sign_before);
    $article_link = $domain."/api/liaoqiu/get_news_detail2?news_id=".$article_id."&sign=".$sign;
    return $article_link;
}


function hxto5uID($hx_account) {
	$arr_tmp = array();
	if(strpos($hx_account, "5usport_")===false) {
		$res["state_code"] = -99;
		$res["state_desc"] = "账号不存在, 错误码:1";
		output($res);
	}
	$arr_tmp = explode("5usport_", $hx_account);
	if(count($arr_tmp)!=2) {
		$res["state_code"] = -99;
		$res["state_desc"] = "账号不存在, 错误码:2";
		output($res);
	}
	$member_id = $arr_tmp[1];
	//数据库里的userID范围为 8位
	if(!preg_match("/^\d{1,8}$/", $member_id)) {
		$res["state_code"] = -99;
		$res["state_desc"] = "账号不存在, 错误码:3";
		output($res);
	}
	return $member_id;
}

function lq_member_init($arr_member) {
    if(@$arr_member["member_logo"]=="")$arr_member["member_logo"] = isset($arr_member["logo"]) ? $arr_member["logo"] : '';
    if(@$arr_member["account"]=="")$arr_member["account"] = isset($arr_member["username"]) ? $arr_member["username"] : '';
    if(@$arr_member["member_id"]=="")$arr_member["member_id"] = isset($arr_member["userid"]) ? $arr_member["userid"] : '';
    if(@$arr_member["nick_name"]=="")$arr_member["nick_name"] = isset($arr_member["nickname"]) ? $arr_member["nickname"] : '';
    if(@$arr_member["hx_username"]=="")$arr_member["hx_username"] = "5usport_".$arr_member["userid"];
    unset($arr_member["logo"]);
    unset($arr_member["userid"]);
    unset($arr_member["nickname"]);
    return $arr_member;
}

function deal_width_symbol($data, $symbol) {
	$arr_ret = array();
	if(strpos($data, $symbol)===false) {
		//不包含
		if(!is_num($data)) {
			$res["state_code"] = -99;
			$res["state_desc"] = "参数错误".$data;
			output($res);
		}
		$arr_ret[] = $data;
		return $data;
	} else {
		$arr_tmp = explode($symbol, $data);
		foreach($arr_tmp as $k => $v) {
			if($v!="") {
				if(!is_num($v)) {
					$res["state_code"] = -99;
					$res["state_desc"] = "参数错误".$v;
					output($res);
				}
				$arr_ret[$k] = $v;
			}
		}
	}
	return implode($symbol, $arr_ret);
}

//处理多个环信账号，校验，并切割，返回多个以 2714,2800,2801 这种格式的字符串
function deal_many_hxaccount($hx_account_list) {
	$arr_hxaccountlist_tmp = array();
	$arr_hxaccountlist_tmp = explode("5usport_", $hx_account_list);
	//数组去空值, 并校验
	foreach($arr_hxaccountlist_tmp as $k => $v) {
		if(trim($v)!="") {
			//数据库里的userID范围为 8位
			if(!preg_match("/^\d{1,8}$/", $v)) {
				$res["state_code"] = -99;
				$res["state_desc"] = "不存在账号:5usport_".$v;
				output($res);
			}
		} else {
			unset($arr_hxaccountlist_tmp[$k]);
		}
	}
	if(count($arr_hxaccountlist_tmp) == 0) {
		//echo "<pre>";print_R($arr_hxaccountlist_tmp);
		$res["state_code"] = -99;
		$res["state_desc"] = "账号错误";
		output($res);
	}
	$member_id = "";
	foreach($arr_hxaccountlist_tmp as $k => $v) {
		$member_id .= $v;
		if($k < count($arr_hxaccountlist_tmp)) {
			$member_id .= ",";
		}
	}
	return $member_id;
}

//根据用户ID获取用户令牌，支持批量
function getinfo_byuserid($userid) {
	//这个是从API接口里读
    $member_id = "";
    if(is_array($userid)){
        foreach ($userid as $k => $v) {
            //Do something here.
            $member_id .= $v;
            if($k < count($userid)){
                $member_id .= ",";
            }
        }
    } else {
        $member_id = $userid;
    }
    $arr_param = array("userid" => $member_id);
	$return_lq_member = json_decode(usport_api("get_member", $arr_param), true);
	return $return_lq_member;
}


/*环信的操作方法*/
function huanxin($request_type, $params) {
	$_CI = &get_instance();
	$_CI->load->library('huanxin');

	$obj_easemob = new Huanxin();
	$res = array(
		"code"=> 0,
		"message"=>"操作成功"
	);
	switch ($request_type) {

		/************** 用户操作 **************************************************************************/

		case "register":
			//添加授权用户（注册单个用户）
			if(!isset($params["user"]) || !isset($params["pass"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$tmp_params = array();
			$tmp_params["username"] = $params["user"];
			$tmp_params["password"] = $params["pass"];
			$hx_res = $obj_easemob->accreditRegister($tmp_params);
			break;

		case "batchregister":
			//添加授权用户（注册批量用户）
			if(!isset($params["user"]) || !isset($params["pass"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$tmp_params1 = array();
			$tmp_params1 = toarr($params["user"]);
			$tmp_params2 = array();
			$tmp_params2 = toarr($params["pass"]);

			if(count($tmp_params1) != count($tmp_params2)) {
				$res["code"] = "400";
				$res["message"] = "账号，密码，个数不相等";
				break;
			}
			$tmp_params = array();
			foreach($tmp_params1 as $key => $val) {
				$tmp_params[] = array(
					"username"	=>	$val,
					"password"	=>	$tmp_params2[$key],
				);
			}
			$hx_res = $obj_easemob->accreditRegister($tmp_params);
			break;

		case "checkuserinfo":
			//查看用户信息
			if(!isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$hx_res = $obj_easemob->userDetails($params["user"]);
			break;

		case "resetpassword":
			//重置用户密码
			if(!isset($params["user"]) || !isset($params["newpass"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$params = array();
			$params["username"] = $params["user"];
			$params["newpassword"] = $params["newpass"];
			$hx_res = $obj_easemob->resetPassword($params);
			break;

		case "setusernickname":
			//设置用户昵称
			if(!isset($params["user"]) || !isset($params["nick"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$params = array();
			$params["username"] = $params["user"];
			$params["nickname"] = $params["nick"];
			$hx_res = $obj_easemob->setNickname($params);
			break;

		case "getuserlist":
			//获取APP用户列表，指定获取数量
			if(!isset($params["limit"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$hx_res = $obj_easemob->userList($params["limit"]);
			break;

		case "deleteuser":
			//删除指定用户
			if(!isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$delete_username = $params["user"];
			$hx_res = $obj_easemob->deleteUser($delete_username);
			break;

		case "addfriend":
			//添加好友
			if(!isset($params["from"]) || !isset($params["to"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$owner_username		=	$params["from"];																									//发起请求用户的ID
			$friend_username	=	$params["to"];																					//添加好友的ID
			$hx_res = $obj_easemob->addFriend($owner_username, $friend_username);
			break;

		case "showfriend":
			//获取指定用户的好友列表
			if(!isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$username = $params["user"];
			$hx_res = $obj_easemob->showFriend($username);
			break;

		case "delfriend":
			//删除好友
			if(!isset($params["from"]) || !isset($params["to"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$owner_username		=	$params["from"];																									//发起请求用户的ID
			$friend_username	=	$params["to"];																					//添加好友的ID
			$hx_res = $obj_easemob->deleteFriend($owner_username, $friend_username);
			break;

		case "blackuser":
			//拉黑
			if(!isset($params["from"]) || !isset($params["to"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$username		=	$params["from"];																									//发起请求用户的ID
			$friend_username	=	toarr($params["to"]);																					//添加好友的ID
			$hx_res = $obj_easemob->addBlackuser($username, $friend_username);
			break;

		case "showblacklist":
			//获取指定用户的好友列表
			if(!isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$username = $params["user"];
			$hx_res = $obj_easemob->showBlacklist($username);
			break;

		case "delblackuser":
			//从黑名单中移除
			if(!isset($params["from"]) || !isset($params["to"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$username		=	$params["from"];																									//发起请求用户的ID
			$friend_username	=	$params["to"];																					//添加好友的ID
			$hx_res = $obj_easemob->delBlackuser($username, $friend_username);
			break;

		case "checkuserstat":
			//查看用户在线状态
			if(!isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$username		=	$params["user"];
			$hx_res = $obj_easemob->checkUserstat($username);
			break;







			/************** 群（聊天室）操作 **************************************************************************/

		case "newgroup":
			//添加群，添加聊天室
			if(!isset($params["groupname"]) || !isset($params["desc"])
			|| !isset($params["public"]) || !isset($params["maxusers"])
			|| !isset($params["approval"]) || !isset($params["owner"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整"."1.".isset($params["groupname"])
                    .";2.".!isset($params["desc"]).";3.".!isset($params["public"])
                    .";4.".!isset($params["maxusers"]).";5.".!isset($params["approval"])
                    .";6.".!isset($params["owner"]);
				break;
			}
			$hx_res = $obj_easemob->createGroup($params);
			break;

		case "getallgroups":
			//获取APP中所有的聊天室(群)
			$hx_res = $obj_easemob->chatGroups();
			break;

		case "modifygroup":
			//修改群信息(聊天室)
			if(!isset($params["group_id"]) || empty($params["groupname"]) && empty($params["description"]) && empty($params["maxusers"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$hx_res = $obj_easemob->modifyGroup($params["group_id"], $params);
			break;

		case "getgroupdetail":
			//获取群详情
			if(!isset($params["group_id"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$group_id  = $params["group_id"];			 												//群ID
			$hx_res = $obj_easemob->chatGroupsDetails($group_id);
			break;

		case "addtogroup":
			//群添加成员
			if(!isset($params["group_id"]) || !isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$tmp_params = array();
			$group_id = $params["group_id"];			 														//群ID
			$user_name = $params["user"];																		//指定username
			$hx_res = $obj_easemob->addGroupsUser($group_id, $user_name);
			break;

		case "batchaddtogroup":
			//群批量添加成员
			if(!isset($params["group_id"]) || !isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			//var_dump(array_values($params["user"]));
			$hx_res = $obj_easemob->batchaddGroupsUser($params["group_id"], array_values($params["user"]));
			break;

		case "getgroupusers":
			//获取指定群的所有用户
			if(!isset($params["group_id"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$group_id = $params["group_id"];																//群ID
			$hx_res = $obj_easemob->groupsUser($group_id);
			break;

		case "deletegroupuser":
			//群踢除好友
			if(!isset($params["group_id"]) || !isset($params["user"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$group_id  = $params["group_id"];			 												//群ID
			$user_name = $params["user"];																					//用户username
			$hx_res = $obj_easemob->delGroupsUser($group_id, $user_name);
			break;

		case "deletegroup":
			//删除群
			if(!isset($params["group_id"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$group_id		=	$params["group_id"];																					//群ID
			$hx_res = $obj_easemob->deleteGroup($group_id);
			break;
                //转让群
                case 'changeGroupOwner':
                        
                    if(!isset($params['newowner']) || !isset($params['group_id']))
                    {
                        $res["code"] = "400";
                        $res["message"] = "参数不完整";
			break;
                    }
                    
                    $group_id = $params['group_id'];
                    unset($params['group_id']);
                    $hx_res = $obj_easemob->changeGroupOwner($group_id, $params);
                    break;






			/************** 聊天（消息）操作 **************************************************************************/

		case "sendtxtmsg":
			/**
			* 某某向某某发送一条文字消息
			*/
			if(!isset($params["from"]) || !isset($params["to"]) || !isset($params["message"]) || !isset($params["target_type"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$user_from = $params["from"]=="" ? "admin" : $params["from"];
			$user_to = $params["to"]=="" ? "" : $params["to"];
			$user_to = explode(",", $user_to);
			$msg = $params["message"]=="" ? "对方发送了一条空消息" : $params["message"];

			$from_user 			=	$user_from;//发送消息者
			$username				=	$user_to;//接收消息者（个人或者群组），必须是数组
			$target_type		=	$params["target_type"];//默认为users，表示发送给个人，如果为groups则发送给群组，上面传的就是群ID了
			$content				=	array("type"=>"txt", "msg"=> $msg."\r\n(IP：".$_SERVER['REMOTE_ADDR'].")");//发送消息的主体，文字(txt)，语音(audio)，图片(img), 视频(video)
			$ext						=	array("attr1"=>"", "attr2"=>"", "attr3"=>"");//自定义参数，可选
			$hx_res = $obj_easemob->hxSend($from_user, $username, $content, $target_type, $ext);
			break;
			
		case "sendlqtxtmsg":
			/**
			* 某某向某某发送一条文字消息
			*/
			if(!isset($params["from"]) || !isset($params["to"]) || !isset($params["message"]) || !isset($params["target_type"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$user_from = $params["from"]=="" ? "admin" : $params["from"];
			$user_to = $params["to"]=="" ? "" : $params["to"];
			$user_to = explode(",", $user_to);
			$msg = $params["message"]=="" ? "对方发送了一条空消息" : $params["message"];

			$from_user 			=	$user_from;//发送消息者
			$username				=	$user_to;//接收消息者（个人或者群组），必须是数组
			$target_type		=	$params["target_type"];//默认为users，表示发送给个人，如果为groups则发送给群组，上面传的就是群ID了
			$content				=	array("type"=>"txt", "msg"=> $msg."\r\n(IP：".$_SERVER['REMOTE_ADDR'].")");//发送消息的主体，文字(txt)，语音(audio)，图片(img), 视频(video)
			$ext						=	array("sender_role"=>1, "sender_nickname"=>$params["sender_nickname"],"sender_icon"=>$params["sender_icon"]);//自定义参数，可选
			if($params["support_team"]) $ext['sender_support_team'] = $params["support_team"];
			$hx_res = $obj_easemob->hxSend($from_user, $username, $content, $target_type, $ext);
			break;

		case "sendimgmsg":
			//某某向某某发送一条图文消息
			if(!isset($params["from"]) || !isset($params["to"]) || !isset($params["secret"]) || !isset($params["target_type"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$user_from = $params["from"]=="" ? "admin" : $params["from"];
			$user_to = $params["to"]=="" ? "" : $params["to"];
			$user_to = explode(",", $user_to);

			$from_user 			=	$user_from;//发送消息者
			$username				=	$user_to;//接收消息者（个人或者群组），必须是数组
			$target_type		=	$params["target_type"];//默认为users，表示发送给个人，如果为groups则发送给群组，上面传的就是群ID了
			$content				=	array("type"=>"img","secret"=>$params["secret"],"url"=>$params["url"]);//发送消息的主体，文字(txt)，语音(audio)，图片(img), 视频(video)
			$ext						=	array("sender_role"=>1, "sender_nickname"=>$params["sender_nickname"],"sender_icon"=>$params["sender_icon"]);//自定义参数，可选
			$hx_res = $obj_easemob->hxSend($from_user, $username, $content, $target_type, $ext);			
			break;

		case "send5umsg":
			/**
			* 发送透传消息
			*/
			if(!isset($params["from"]) || !isset($params["to"]) || !isset($params["type"])
			|| !isset($params["action"]) || !isset($params["target_type"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$user_from = $params["from"]=="" ? "admin" : $params["from"];
			$user_to = $params["to"]=="" ? "" : $params["to"];
			$type = $params["type"]=="" ? "cmd" : $params["type"];
			$action = $params["action"]=="" ? "action" : $params["action"];
			$ext = $params["ext"]=="" ? array() : $params["ext"];       //自定义属性

			$user_to = explode(",", $user_to);

			$from_user 			=	$user_from;//发送消息者
			$username				=	$user_to;//接收消息者（个人或者群组），必须是数组
			$target_type		=	$params["target_type"];//默认为users，表示发送给个人，chatgroups为群组。如果为chatgroups则发送给群组，上面传的就是群ID了
			$content				=	array("type"=> $type, "action"=> $action);//发送消息的主体，文字(txt)，语音(audio)，图片(img), 视频(video)
			$hx_res = $obj_easemob->hxSend($from_user, $username, $content, $target_type, $ext);
			break;

		case "sendshowmsg":
			/**
			* 发送节目透传消息，用于即时改变banner
			*/
			if(!isset($params["to"]) || !isset($params["type"])
			|| !isset($params["action"]) || !isset($params["target_type"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$user_to = $params["to"]=="" ? "" : $params["to"];
			$type = $params["type"]=="" ? "cmd" : $params["type"];
			$action = $params["action"]=="" ? "action" : $params["action"];
			$ext = $params["ext"]=="" ? array() : $params["ext"];       //自定义属性

			$user_to = explode(",", $user_to);

			$username				=	$user_to;//接收消息者（个人或者群组），必须是数组
			$target_type		=	$params["target_type"];//默认为users，表示发送给个人，chatgroups为群组。如果为chatgroups则发送给群组，上面传的就是群ID了
			$content				=	array("type"=> $type, "action"=> $action);//发送消息的主体，文字(txt)，语音(audio)，图片(img), 视频(video)
			$hx_res = $obj_easemob->hxSend("", $username, $content, $target_type, $ext);
			break;
		case "dlfiles":
			/**
			* 下载文件
			*/
			if(!isset($params["file_name"]) || !isset($params["file_type"]) || !isset($params["hx_url"])
			|| !isset($params["secret"]) || !isset($params["path"]) || !isset($params["thumb"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}


			$file_name = $params["file_name"];
			$file_type = $params["file_type"];
			$save_path = $params["path"];
			$hx_url = $params["hx_url"];
			$secret = $params["secret"];
			$is_thumb = $params["thumb"];
			$hx_res = $obj_easemob->downloadHXFiles($hx_url, $file_name.$file_type, $save_path,$secret, $is_thumb);
			break;
			
		//上传文件	
		case "uploadfile":
			$hx_res = $obj_easemob->uploadsHXImage($params);
			break;
		case "exportlog":
			/**
			* 导出指定条件的聊天记录
			*/
			if(!isset($params["sql"])) {
				$res["code"] = "400";
				$res["message"] = "参数不完整";
				break;
			}
			$sql = $params["sql"];
			$hx_res = $obj_easemob->chatRecord2($sql);
			break;
			
// 创建新版聊天室   /////////////////////////////////////////////////////////////////////////////////////			
		case "newroom":
			$hx_res = $obj_easemob->createRoom($params);
			break;
		default:
			$hx_res = array("code"=>404, "message"=>"指令出错");
			break;

	}

	$res["huanxin_result"] = json_decode($hx_res, true);
    $logs_content = "\r\n\r\n===============[请求环信操作：".$request_type."的返回结果]===[".date("Y-m-d H:i:s")."]============\r\n<pre>".http_build_query($res)."</pre>\r\n";
    write_file_log($logs_content, "liaoqiu", 'log_error', FALSE);
	return $res;
}

function huanxin_result($huanxin_result, $type="huanxin_result") {
	$reason = "服务器繁忙，请稍候重试";
	//环信建群失败
	if(!array_key_exists("huanxin_result", $huanxin_result)) {
		$res["state_code"] = -4;
		$res["state_desc"] = $reason."1";
        write_logs(1, "huanxin_result1", $type, $huanxin_result);
		output($res);
	}
	if(array_key_exists("error", $huanxin_result["huanxin_result"])) {
		if($huanxin_result["huanxin_result"]["error"]!="") {
			 $reason = "操作失败, 错误原因：".$huanxin_result["huanxin_result"]["error"];
		}
		$res["state_code"] = -4;
		$res["state_desc"] = $reason."2";
        write_logs(1, "huanxin_result2", $type, $huanxin_result);
		output($res);
	}
    /*
	if(!array_key_exists("data", $huanxin_result["huanxin_result"])) {
		$res["state_code"] = -4;
		$res["state_desc"] = $reason."3";
        write_logs(1, "huanxin_result3", $type, $huanxin_result);
		output($res);
	}
    */
}

function toarr($str) {
	if(strpos($str, ",")!==false) {
		return explode(",", $str);
	} else {
		return array($str);
	}
}

function getfile($dir){
	$file_arr = scandir($dir);
	$arr = array();
	foreach ($file_arr as $v) {
		if($v[0] != '.') $arr[] = $v;
	}
	return $arr;
}

/**
 *  注册
 * 
 * @param array $params
 * @return array
 */
function batchregister_huanxin($params = array(), &$bool_hx_reg_result, &$hx_username, &$hx_userpass)
{
    if(empty($params) || empty($params['username']) || empty($params['userid']))
    {
        return false;
    }
    
    $usport_userid = $params["userid"];
    $hx_username = "5usport_".$usport_userid;
    $hx_userpass = rand(1000, 999999).substr(time(), 4, 6);
    $member_logo = $params["member_logo"];

    $hx_action = "batchregister";
    $params = array("user" => $hx_username, "pass"=> $hx_userpass);
    $huanxin_result = @huanxin($hx_action, $params);
    
    if($huanxin_result["code"]!="0" || !array_key_exists("entities", $huanxin_result["huanxin_result"])) 
    {
        $bool_hx_reg_result = false;
    } 
    else if(count($huanxin_result["huanxin_result"]["entities"])!=1) 
    {
        $bool_hx_reg_result = false;
    } 
    else if($huanxin_result["huanxin_result"]["entities"][0]["uuid"]=="") 
    {
        $bool_hx_reg_result = false;
    }
    
    return $huanxin_result;
}


function getmemberdetail($user_id_or_array) {
    $tmp = getinfo_byuserid($user_id_or_array);
    $tmp2 = isset($tmp["user_detail"]) ? $tmp["user_detail"] : array();
    return add_account_hx_username($tmp2);
}



function get_huanxin_config_field($field)
{
    $_CI = &get_instance();
    $_CI->config->load('huanxin_config');
    
    return $_CI->config->item($field);
}

/**
 * 获取环信用户名
 * 
 * @param int $member_id
 */
function get_huanxin_account($member_id)
{
   if(empty($member_id))
   {
       return FALSE;
   }
   
   $prefix = '5usport_';
   
   return $prefix . $member_id ;
}

?>