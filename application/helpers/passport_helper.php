<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 取discuz相册内容URL
function passport_get_discuz_album($uid)
{
	$_CI =& get_instance();
	$_CI->load->model('member_model');
	$_CI->load->model('group/home_model');
	
	// 根据uid取ucenter的用户ID
	$obj = $_CI->member_model->getSsoMember($uid + 0);
	$row = array();
	if(is_object($obj))
	{
		if($obj->ucuserid + 0 > 0)
		{
			$row = $_CI->home_model->getAblum($obj->ucuserid + 0);
		}
	}
	
	$temp_album = array();
	for($j = 0; $j < count($row); $j++)
	{
		$_tmp = array();
		if($row[$j]['thumb'] == 1)
		{
			$_tmp['img_thumb_url'] = $_CI->config->item('group_url').'/data/attachment/album/'.$row[$j]['filepath'].'.thumb.jpg';
		} else {
			$_tmp['img_thumb_url'] = '';
		}
		$_tmp['img_url'] = $_CI->config->item('group_url').'/data/attachment/album/'.$row[$j]['filepath'];
		$temp_album[] = $_tmp;
	}
	return $temp_album;
}

// 根据第三方信息查用户
function passport_get_member_by_oauth($arr_param)
{
	$from = trim($arr_param['from']);
	$connectid = trim($arr_param['connectid']);
	
	$_CI =& get_instance();
	$_CI->load->model('member_model');
	
	$data = array();
	$data['from'] = $from;
	$data['connectid'] = $connectid;
	
	$row = $_CI->member_model->get_rows($data);
	return $row;
}

// 根据token查type
function passport_token_to_type($token)
{
	$_CI =& get_instance();
	$_CI->load->model('member_model');
	$arr_param = array();
	$arr_param['token'] = $token;
	$row = $_CI->member_model->getPassportTokenTmp($arr_param);
	return $row;
}

// 生成手机注册验证码并发短信
function passport_mobile_create_verify_code($mobile)
{
	$arr_return = array('success' => FALSE, 'message' => '', 'verify_sms_code' => '');
	// 生成校验码
	$how = 6;
	// $alpha = 'abcdefghijkmnpqrstuvwxyz';
	$number = '1234567890';
	$verify_sms_code = '';
	
	for($i = 0; $i < $how; $i++)  
	{     
		// $alpha_or_number = mt_rand(0, 1);
		// $str = $alpha_or_number ? $alpha : $number;
		$str = $number;
		$which = mt_rand(0, strlen($str)-1);
		$code = substr($str, $which, 1);
		$j = !$i ? 4 : $j+15;
		$verify_sms_code .= $code;
	}
	
	if(is_mobile($mobile))
	{
		$content = '手机注册验证，您的短信验证码是：'.$verify_sms_code;
		$send_seccess = passport_write_sms_log($mobile, $content);
		
		if($send_seccess === TRUE)
		{
			$arr_return['success'] = TRUE;
			$arr_return['message'] = '发送成功，请输入短信验证码';
			$arr_return['verify_sms_code'] = $verify_sms_code;
		}
		else
		{
			$arr_return['message'] = $send_seccess;
		}
	}
	else
	{
		$arr_return['message'] = '不是一个有效的手机号码，请重新填写';
	}
	return $arr_return;
}

// 检查昵称是否可以注册
function passport_nickname_can_register($nickname)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	if(empty($nickname))
	{
		$arr_return['message'] = '昵称没有填写';
	}
	else
	{
		$_CI =& get_instance();
		$_CI->load->model('member_model');
		$_CI->load->library('api_out');
		$nickname = is_gbk($nickname) ? arr_to_utf8($nickname) : $nickname;
		
		$row = $_CI->member_model->get(array('username' => $nickname));	// username不能是nickname
		$row2 = $_CI->member_model->getMemberByNickName($nickname);		// nickname也不能存在
		
		// zhangjh 2015-03-20 多查询论坛是否有记录
		$param = array();
		$param['action'] = 'get_userid_uc';
		$param['username'] = $nickname;
		$return = $_CI->api_out->phpcms($param);

		if($return['success'] === TRUE)	// 有记录
		{
			$have_uc_record = TRUE;
		}
		else
		{
			$have_uc_record = FALSE;
		}
		
		if(is_object($row2) || $row || $have_uc_record)
		{
			$arr_return['message'] = '昵称已经存在';
		}
		elseif(!is_username($nickname))
		{
			$arr_return['message'] = '昵称有误';
		}
		else
		{
			$arr_return['success'] = TRUE;
		}
	}
	return $arr_return;
}

// 检查手机号码是否可以注册
function passport_mobile_can_register($mobile)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	if(is_mobile($mobile) && trim($mobile) != '')
	{
		$_CI =& get_instance();
		$_CI->load->model('member_model');
		$row = $_CI->member_model->get(array('username' => $mobile));
		
		$data = array();
		$data['mobile'] = $mobile;
		$data['all_search'] = 1;
		$row2 = $_CI->member_model->getMemberByMobile($data);
		
		if($row || count($row2) > 0)
		{
		}
		else
		{
			$arr_return['success'] = TRUE;
		}
	}
	return $arr_return;
}

// 检查Email是否可以找回（已存在并已绑定）
function passport_email_can_find($email)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	if(empty($email))
	{
		$arr_return['message'] = 'Email没有填写'; 
	}
	if($arr_return['message'] == '')
	{
		if(!is_email($email))
		{
			$arr_return['message'] = '不是一个有效的Email';
		}
	}
	
	if($arr_return['message'] == '')
	{
		$_CI =& get_instance();
		$_CI->load->model('member_model');
		
		$data = array('email' => $email);
		$row = $_CI->member_model->get($data);
		
		if(count($row) == 0)
		{
			$arr_return['message'] = 'Email不存在';
		}
		else
		{
			$user_detail = $_CI->member_model->getMemberDetail($row['userid']);
			if(is_object($user_detail))
			{
				if($user_detail->bind_email != 1)
				{
					$arr_return['message'] = 'Email没有绑定';
				}
			}
			else
			{
				$arr_return['message'] = 'Email没有绑定';
			}
		}
	}
	
	if($arr_return['message'] == '')
		$arr_return['success'] = TRUE;
	return $arr_return;
}

// 检查手机是否可以找回（已存在并已绑定）
function passport_mobile_can_find($mobile)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	if(is_mobile($mobile) && trim($mobile) != '')
	{
		$_CI =& get_instance();
		$_CI->load->model('member_model');

		$data = array();
		$data['mobile'] = $mobile;
		$data['all_search'] = 1;
		$row = $_CI->member_model->getMemberByMobile($data);
		if(count($row) > 0)
		{
			if($row['bind_mobile'] == 1)
			{
				$arr_return['success'] = TRUE;
			}
			else
			{
				$arr_return['message'] = '该手机没有绑定';
			}
		}
		else
		{
			$arr_return['message'] = '不存在该手机的记录';
		}
	}
	else
	{
		$arr_return['message'] = '不是一个有效的手机号码';
	}
	return $arr_return;
}

// 检查Email是否可以注册或修改
function passport_email_can_register($email)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	if(empty($email))
	{
		$arr_return['message'] = 'Email没有填写'; 
	}
	
	if($arr_return['message'] == '')
	{
		if(!is_email($email))
		{
			$arr_return['message'] = '不是一个有效的Email';
		}
	}
	
	if($arr_return['message'] == '')
	{
		$_CI =& get_instance();
		$_CI->load->model('member_model');
		
		$data = array('email' => $email);
		$row = $_CI->member_model->get($data);
		if(count($row) > 0)
			$arr_return['message'] = 'Email已经存在1';
		
		if($arr_return['message'] == '')
		{
			$data = array('username' => $email);
			$row2 = $_CI->member_model->get($data);
			if(count($row2) > 0)
				$arr_return['message'] = 'Email已经存在2';
		}
		
		if($arr_return['message'] == '')
		{
			// zhangjh 2015-04-07 多查询论坛是否有记录
			$_CI->load->library('api_out');
			$param = array();
			$param['action'] = 'get_userid_uc_by_email';
			$param['email'] = $email;
			$return = $_CI->api_out->phpcms($param);

			if($return['success'] === TRUE)	// 有记录
			{
				$arr_return['message'] = 'Email已经存在3';
			}
		}
		
	}
	
	if($arr_return['message'] == '')
		$arr_return['success'] = TRUE;
	return $arr_return;
}

// 生成token的值
function passport_make_token($data)
{
	list($usec, $sec) = explode(" ", microtime());
	$microtime = ((float)$usec + (float)$sec);
	return md5(http_build_query($data).$microtime.rand(10000, 99999));
}

// app分步注册时，操作token表
function passport_token($data)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	$_CI =& get_instance();
	$_CI->load->model('member_model');
	
	// 如果是邮箱注册第一步
	if($data['method'] == 'register' && $data['type'] == 'email')
	{
		if($data['step'] == '1')
		{
			// 查询是否之前有记录
			$arr_param = array();
			$arr_param['method'] = $data['method'];
			$arr_param['is_deleted'] = '0';
			$arr_param['type'] = $data['type'];
			$arr_param['step'] = $data['step'];
			$arr_param['email'] = $data['email'];
			$row = $_CI->member_model->getPassportTokenTmp($arr_param);
			if(count($row) > 0) // 如果之前有记录，返回这个token，暂时没有时间限制
			{
			}
			else
			{
				if(isset($data['newpwd']))
					unset($data['newpwd']);
				if(isset($data['timestamp']))
					unset($data['timestamp']);
				$id = $_CI->member_model->setPassportTokenTmp($data);
				$row = $_CI->member_model->getPassportTokenTmp(array('id' => $id));
			}
			if(count($row) == 1)
			{
				$arr_return['success'] = TRUE;
				$arr_return['message'] = $row[0]['token'];
			}
			else
			{
				if(count($row) == 0)
				{
					$arr_return['message'] = '没有生成token';
				}
				elseif(count($row) > 1)
				{
					$arr_return['message'] = 'email数据重复';
				}
			}
		}
		elseif($data['step'] == '2')
		{
			$arr_param = array();
			$arr_param['method'] = $data['method'];
			$arr_param['is_deleted'] = '0';
			$arr_param['type'] = $data['type'];
			$arr_param['step'] = '1';
			$arr_param['token'] = $data['token'];
			$row = $_CI->member_model->getPassportTokenTmp($arr_param);
			if(count($row) == 1)
			{
				$arr_param = array();
				$arr_param['step'] = '2';
				$arr_param['nickname'] = $data['nickname'];
				$arr_param['username'] = $data['nickname'];
				$arr_param['is_deleted'] = '1';
				$_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
				
				$arr_return['success'] = TRUE;
				$arr_return['userinfo'] = $_CI->member_model->getPassportTokenTmp(array('token' => $row[0]['token']));
			}
			else
			{
				$arr_return['message'] = 'token不存在或者无效';
			}
		}
	}
	elseif(($data['method'] == 'register' || $data['method'] == 'resetpwd' || $data['method'] == 'bind') && $data['type'] == 'mobile')
	{
		if($data['step'] == '1')
		{
			// 如果是找回密码的，要保证手机号唯一
			if($data['method'] == 'resetpwd')
			{
				$_row = $_CI->member_model->getUseridByMobile($data['mobile']);
				if(count($_row) > 1)
				{
					$arr_return['success'] = FALSE;
					$arr_return['message'] = '手机号码不唯一';
				}
			}
			
			if($arr_return['message'] == '')
			{
				// 查询是否之前有记录
				$arr_param = array();
				$arr_param['method'] = $data['method'];
				$arr_param['is_deleted'] = '0';
				$arr_param['type'] = $data['type'];
				// $arr_param['step'] = $data['step'];
				$arr_param['mobile'] = $data['mobile'];
				$row = $_CI->member_model->getPassportTokenTmp($arr_param);
				if(count($row) > 0) // 如果之前有记录，返回这个token，暂时没有时间限制
				{
					$need_send_new = FALSE;
					$timestamp = trim(@$data['timestamp']) + 0;
					$log_time = $row[0]['log_time'];
					$my_time = time();
					$cha = abs($log_time - $my_time);
					if($cha > 60) // 检查 timestamp 60s 内重新发sms
					{
						// 作废之前的记录
						$arr_param = array();
						$arr_param['is_deleted'] = '1';
						$_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
						$need_send_new = TRUE;
					}
				}
				else
				{
					$need_send_new = TRUE;
				}
				
				if($need_send_new)	// 是否需要新发sms
				{
					$return = passport_mobile_create_verify_code($data['mobile']);
					if($return['success'] === TRUE)
					{
						$data['password'] = '';
						//$data['encrypt'] = '';
						$data['sms_verify'] = $return['verify_sms_code'];
						
						if(isset($data['newpwd']))
							unset($data['newpwd']);
						if(isset($data['timestamp']))
							unset($data['timestamp']);
						
						$id = $_CI->member_model->setPassportTokenTmp($data);
						$row = $_CI->member_model->getPassportTokenTmp(array('id' => $id));
					}
					else
					{
						$arr_return['message'] = $return['message'];
						return $arr_return;
					}
				}
				
				if(count($row) == 1)
				{
					$arr_return['success'] = TRUE;
					$arr_return['message'] = $row[0]['token'];
				}
				else
				{
					$arr_return['message'] = 'token数据重复';
				}
			}
		}
		elseif($data['step'] == '2')
		{
			// 查询是否之前有记录
			$arr_param = array();
			$arr_param['method'] = $data['method'];
			$arr_param['is_deleted'] = '0';
			$arr_param['type'] = $data['type'];
			$arr_param['step'] = '1';
			$arr_param['token'] = $data['token'];
			$row = $_CI->member_model->getPassportTokenTmp($arr_param);
			if(count($row) == 1)
			{
				if($row[0]['sms_verify'] == $data['sms_verify'] && $row[0]['sms_verify'] != '')
				{
					$arr_return['success'] = TRUE;
					$arr_param = array();
					$arr_param['step'] = '2';
					if($data['method'] == 'bind')	// 如果是绑定新手机，第2步就是最后一步
					{
						$arr_param['is_deleted'] = '1';
					}
					
					$_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
					if($data['method'] == 'bind')	// 如果是绑定新手机，第2步多返回信息
					{
						$arr_return['userinfo'] = $_CI->member_model->getPassportTokenTmp(array('token' => $row[0]['token']));
					}
				}
				else
				{
					$arr_return['message'] = '验证码不正确';
				}
			}
			else
			{
				$arr_return['message'] = 'token不存在或者已验证过一次';
			}
		}
		elseif($data['step'] == '3') // 输入密码
		{
			$arr_param = array();
			$arr_param['method'] = $data['method'];
			$arr_param['is_deleted'] = '0';
			$arr_param['type'] = $data['type'];
			$arr_param['step'] = '2';
			$arr_param['token'] = $data['token'];
			$row = $_CI->member_model->getPassportTokenTmp($arr_param);
			if(count($row) == 1)
			{
				if($data['method'] == 'resetpwd')	// 如果是手机找回密码，要修改密码
				{
					$_row = $_CI->member_model->getUseridByMobile($row[0]['mobile']);
					if(count($_row) != 1)
					{
						$arr_return['success'] = FALSE;
						$arr_return['message'] = '手机号码不唯一';
					}
					else
					{
						$userid = $_row[0]['userid'];
					}
				}
				
				if($arr_return['message'] == '')
				{
					$arr_return['success'] = TRUE;
					$arr_param = array();
					$arr_param['step'] = '3';
					if($data['method'] == 'resetpwd')	// 如果是手机找回密码，第3步就是最后一步
					{
						$arr_param['is_deleted'] = '1';
					}
					$arr_param['password'] = trim(@$data['newpwd']) == '' ? trim(@$data['password']) : trim(@$data['newpwd']);	// 明码
					$arr_param['from'] = trim(@$data['from']);
					$arr_param['connectid'] = trim(@$data['connectid']);
					$arr_param['remote_avatar'] = trim(@$data['remote_avatar']);
					$_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
					
					if($data['method'] == 'resetpwd')	// 如果是手机找回密码，第3步多返回信息
					{
						$arr_return['userinfo'] = $_CI->member_model->getPassportTokenTmp(array('token' => $row[0]['token']));
						$arr_return['userinfo'][0]['userid'] = $userid;
					}
				}
				
			}
			else
			{
				$arr_return['message'] = 'token不存在或者已修改过一次';
			}
		}
		elseif($data['step'] == '4') // 输入昵称并注册
		{
			$arr_param = array();
			$arr_param['method'] = $data['method'];
			$arr_param['is_deleted'] = '0';
			$arr_param['type'] = $data['type'];
			$arr_param['step'] = '3';
			$arr_param['token'] = $data['token'];
			$row = $_CI->member_model->getPassportTokenTmp($arr_param);
			if(count($row) == 1)
			{
				$arr_param = array();
				$arr_param['step'] = '4';
				$arr_param['nickname'] = $data['nickname'];
				$arr_param['username'] = $data['nickname'];
				$arr_param['is_deleted'] = '1';
				$arr_param['email'] = 'p_'.$row[0]['mobile'].'@example.com'; // 暂定
				$_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
				
				$arr_return['success'] = TRUE;
				$arr_return['userinfo'] = $_CI->member_model->getPassportTokenTmp(array('token' => $row[0]['token']));
			}
			else
			{
				$arr_return['message'] = 'token不存在或者无效';
			}
		}
	}
	
	return $arr_return;
	
/*
CREATE TABLE IF NOT EXISTS `usport_passport_token_tmp` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`token` char(32) NOT NULL COMMENT 'token字符串',
`is_deleted` int(1) unsigned NOT NULL COMMENT '0' COMMENT '是否已经删除 0/1',
`log_time` int(11) unsigned NOT NULL COMMENT '0' COMMENT '时间',
`method` char(10) NOT NULL COMMENT '应用方法 register/...',
`type` char(10) NOT NULL COMMENT '应用类型 email/mobile/...',
`step` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '第几步，可以删除记录',
`from` char(10) NOT NULL DEFAULT '' COMMENT '使用来源 qq/sina/weixin/...',
`connectid` char(64) NOT NULL DEFAULT '' COMMENT '使用来源用户标识',
`remote_avatar` char(100) DEFAULT NULL COMMENT '使用来源用户头像',
`username` char(32) DEFAULT '',
`nickname` char(20) DEFAULT '',
`mobile` char(32) DEFAULT '',
`sms_verify` char(10) DEFAULT '' COMMENT 'mobile的短信验证码',
`email` char(32) DEFAULT '',
`password` char(32) DEFAULT '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '用户中心token记录临时表';
*/
}

// 用户登出
function passport_logout($hide = 0)
{
    $_CI =& get_instance();
    $arr_member = get_member_info();
    $_CI->session->del($arr_member);
        
    // 通过接口取UC的配置
    $_CI->load->library('api_out');
    $param = array();
    $param['action'] = 'get_config_uc';
    $arr_config = $_CI->api_out->phpcms($param);
    
    $arr_js = array('');
    //if($arr_config['ucuse'] === '1')
    if(TRUE)
    {
        $param = array();
        $param['action'] = 'set_synlogout_uc';
        
        $arr_out = $_CI->api_out->phpcms($param);
        if($arr_out['success'] === TRUE)
        {
             $js_code = $arr_out['js_code'];
             
             // 分析js_code取js的src返回给iframe调用
             $regex = "|src=\"(.*)\"|U";
             preg_match_all($regex, $js_code, $_tmp_arr, PREG_PATTERN_ORDER);
             // $arr_js = $_tmp_arr[1];    // 取第一个
             $_arr_js = $_tmp_arr[1];

             // m1 zhangjh tuike.cc 要多取curl
             for($i = 0; $i < count($_arr_js); $i++)
             {
                if(strpos($_arr_js[$i], 'tuike') !== FALSE)
                {
                    $return_tuike = curl_access($_arr_js[$i]);
                    $regex = '|src=(.*) |U';
                    preg_match_all($regex, $return_tuike, $_tmp_arr_tuike, PREG_PATTERN_ORDER);
                    $_tmp_str = trim(@$_tmp_arr_tuike[1][0]);
                    $tuike_api_url = str_replace('\"', '', $_tmp_str);
                    $arr_js[$i] = $tuike_api_url;
                }
                else
                {
                    $arr_js[$i] = $_arr_js[$i];
                }
             }
                         
             $uc_msg = '';
        }
        else
        {
            $uc_msg = '同步退出失败';
        }
    }
    else
    {
        $uc_msg = '后台没有设置同步';
    }
    
    $arr_return = array('success' => TRUE, 
                        'message' => '登出成功', 
                        'arr_js' => $arr_js,
                        'uc_msg' => $uc_msg);
    if($hide == 1)
    {
        $res = '';
        for($i = 0; $i < count($arr_js); $i++)
        {
            if(trim($arr_js[$i]) != '')
            {
                $res .= '<script type="text/javascript" src="'.$arr_js[$i].'" reload="1"></script>';
            }
        }
         $res .= '<script>setTimeout(function() {
                            parent.window.location.href = "http://'.trim($_CI->input->get('referer', TRUE)).'";
                        }, 1000);</script>';
                            
        die('登出成功'.$res);
    }
    elseif($hide == 2)
    {
        $res = '';
        for($i = 0; $i < count($arr_js); $i++)
        {
            if(trim($arr_js[$i]) != '')
            {
                $res .= '<script type="text/javascript" src="'.$arr_js[$i].'" reload="1"></script>';
            }
        }
        die('登出成功'.$res);
    }
    elseif($hide == 3)
    {
        $res = '';
        for($i = 0; $i < count($arr_js); $i++)
        {
            if(trim($arr_js[$i]) != '')
            {
                $res .= '<script type="text/javascript" src="'.$arr_js[$i].'" reload="1"></script>';
            }
        }
        echo $res;
    }
    else
    {
        echo json_encode($arr_return);
    }
}

// 第三方强制补充资料，如果不补充，则成未登录状态
function passport_kickout_login()
{
    $arr_member = get_member_info();
    if($arr_member['logined']) // 已经登录了
    {
        $_CI =& get_instance();
        $_CI->load->model('member_model');
        $data = array('userid' => $arr_member['user_id'] + 0);
        $row = $_CI->member_model->get($data); 
        if(count($row) > 0)
        {
            $from = $row['from'];
            $islock = $row['islock'];
            $old_email = $row['email'];
            $encrypt = $row['encrypt']; 
            $phpssouid = $row['phpssouid'] + 0;
            
            $row_sso = $_CI->member_model->getSsoMember($phpssouid);
            $ucuserid = $row_sso->ucuserid + 0;
            
            $_tmp = explode('_', $row['username']);
            
            if($islock == 0) // 需要修改的条件
            {
                // $mix_return = '你已经完善过，没有修改的需要';
            }
            else
            {
                $url = current_url();
                
                $pos_sina = strpos($url,'sinalogin');
                $pos_qq = strpos($url, 'qqlogin');
                
                if($pos_sina === FALSE && $pos_qq === FALSE && trim($url) != '')
                {
                    passport_logout(3);
                    header("Location:".$url);
                }
                else
                {
                    // $mix_return = '请填写';
                }
            }
        }
        else
        {
            // $mix_return = '没有这个账号';
        }
        // echo $mix_return;
    }
}

// 生成头像
function passport_avatar_create($userid, $base64_image_content)
{
	$message = FALSE;
	$message_add = '';
    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result))
    {
        
        $avatar_api_dir = get_config_field('avatar_api_dir');
        
        // 先建文件夹
        $have_exist = passport_avatar_exist($userid);
        if(!$have_exist)
        {
			$message_add .= '不存在文件';
			$message_add .= '|'.passport_avatar_home_set($userid, $avatar_api_dir.'data'.DS.'avatar');
			$message_add .= "\n";
        }
		else
		{
			$message_add .= '已存在文件';
			$message_add .= "\n";
		}
        
        $type = $result[2];
        
        $path_tmp = $avatar_api_dir.'data'.DS.'tmp'.DS;
        $tmp_file = $path_tmp.'apiupload'.$userid.".".$type;    // 临时保存的文件
        
        $big_file = passport_avatar_file($userid, 'big', TRUE, FALSE);
        $middle_file = passport_avatar_file($userid, 'middle', TRUE, FALSE);
        $small_file = passport_avatar_file($userid, 'small', TRUE, FALSE);
        
        // 然后保存到tmp
        $fp = @fopen($tmp_file, 'wb');
        @fwrite($fp, base64_decode(str_replace($result[1], '', $base64_image_content)));
        @fclose($fp);
        
        // big 200x200 middle 120x120 small 48x48
        include_once(BASEPATH.'../application/libraries/avatar/ImageCore.func.php'); // 图像缩放
        
        // 最后转jpg
        if($type == 'gif')
        {
            $image = imagecreatefromgif($tmp_file);
            imagejpeg($image, $big_file);
            @vxResize($big_file, $big_file, 200, 200);
            @vxResize($big_file, $middle_file, 120, 120);
            @vxResize($big_file, $small_file, 48, 48);
            @unlink($tmp_file);
            $message = TRUE;
        }
        elseif($type == 'png')
        {
            $image = imagecreatefrompng($tmp_file);
            imagejpeg($image, $big_file);
            @vxResize($big_file, $big_file, 200, 200);
            @vxResize($big_file, $middle_file, 120, 120);
            @vxResize($big_file, $small_file, 48, 48);
            @unlink($tmp_file);
            $message = TRUE;
        }
        elseif($type == 'jpeg' || $type == 'jpg')
        {
            $image = imagecreatefromjpeg($tmp_file);
            imagejpeg($image, $big_file);
            @vxResize($big_file, $big_file, 200, 200);
            @vxResize($big_file, $middle_file, 120, 120);
            @vxResize($big_file, $small_file, 48, 48);
            @unlink($tmp_file);
            $message = TRUE;
        }
        else
        {
			$message = '错误的类型';
        }
		$message_add .= '|userid='.$userid;
		$message_add .= "\n".'|tmp_file='.$tmp_file;
		$message_add .= "\n".'|middle_file='.$middle_file;
		$message_add .= "\n".'|type='.$type;
		$message_add .= "\n".'|缩放目录='.$avatar_api_dir.'ImageCore.func.php';
		$message_add .= "\n".'|生成目录='.$avatar_api_dir.'data'.DS.'avatar'; 
    }
    else
    {
		$message = '不是一个有效的图片编码';
    }
	//write_file_log($message.'|'.$message_add, 'liaoqiu', 'log_error', FALSE);
	return $message;
}

// 头像是否存在
function passport_avatar_exist($userid, $size = 'small')
{
    $file = passport_avatar_file($userid, $size, TRUE);
    if(file_exists($file))
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

// 新建头像文件夹
function passport_avatar_home_set($userid, $dir = '.')
{
	$message = 'init';
    $userid = sprintf("%09d", $userid);
    $dir1 = substr($userid, 0, 3);
    $dir2 = substr($userid, 3, 2);
    $dir3 = substr($userid, 5, 2);
    
	// 0
	if(!is_dir($dir))
	{
		$bln_success = mkdir($dir, 0777);
		if($bln_success)
		{
			$message .= '|mkdir('.$dir.')=success0|'.is_dir($dir);
		}
		else
		{
			$message .= '|mkdir('.$dir.')=false0|'.is_dir($dir);
		}
	}
	else
	{
		$message .= '|mkdir('.$dir.')=have0|'.is_dir($dir);
	}
	$message .= "\n";
	
	// 1
	if(!is_dir($dir.DS.$dir1))
	{
		$bln_success = mkdir($dir.DS.$dir1, 0777);
		if($bln_success)
		{
			$message .= '|mkdir('.$dir.DS.$dir1.')=success1|'.is_dir($dir.DS.$dir1);
		}
		else
		{
			$message .= '|mkdir('.$dir.DS.$dir1.')=false1|'.is_dir($dir.DS.$dir1);
		}
	}
	else
	{
		$message .= '|mkdir('.$dir.DS.$dir1.')=have1|'.is_dir($dir.DS.$dir1);
	}
	$message .= "\n";
	
	// 2
    if(!is_dir($dir.DS.$dir1.DS.$dir2))
	{
		$bln_success = mkdir($dir.DS.$dir1.DS.$dir2, 0777);
		if($bln_success)
		{
			$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.')=success2|'.is_dir($dir.DS.$dir1.DS.$dir2);
		}
		else
		{
			$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.')=false2|'.is_dir($dir.DS.$dir1.DS.$dir2);
		}
	}
	else
	{
		$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.')=have2|'.is_dir($dir.DS.$dir1.DS.$dir2);
	}
	$message .= "\n";
	
	// 3
	if(!is_dir($dir.DS.$dir1.DS.$dir2.DS.$dir3))
	{
		$bln_success = mkdir($dir.DS.$dir1.DS.$dir2.DS.$dir3, 0777);
		if($bln_success)
		{
			$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.DS.$dir3.')=success3|'.is_dir($dir.DS.$dir1.DS.$dir2.DS.$dir3);
		}
		else
		{
			$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.DS.$dir3.')=false3|'.is_dir($dir.DS.$dir1.DS.$dir2.DS.$dir3);
		}
	}
	else
	{
		$message .= '|mkdir('.$dir.DS.$dir1.DS.$dir2.DS.$dir3.')=have3|'.is_dir($dir.DS.$dir1.DS.$dir2.DS.$dir3);
	}
	
	return $message;
}

// 显示头像
function passport_avatar_show($userid, $size = 'small', $returnsrc = FALSE, $resize = '') 
{
   
    $avatar_api_url = get_config_field('avatar_api_url');
    $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'small';
    $avatarfile = passport_avatar_file($userid, $size);
	$avatarfile = strtr($avatarfile,"\\","/");//liu+
	// zhangjh 2015-04-01 检查是否有这个文件
	$add_str = '';
        $image_type = '.jpg';
        
	if(passport_avatar_exist($userid))  //用于拉头像，这里是非！
	{
		$file = passport_avatar_file($userid, $size, TRUE);
		$file_time = filemtime($file);
		if(trim($resize) != '')
		{
			$_tmp = explode('x', $resize);
			$add_str = 'width="'.$_tmp[0].'" height="'.$_tmp[1].'" ';
		}
		
		return $returnsrc ? 
		$avatar_api_url.'/data/avatar/'.$avatarfile.'?ft='.$file_time : 
		'<img '.$add_str.'src="'.$avatar_api_url.'/data/avatar/'.$avatarfile.'" onerror="this.onerror=null;this.src=\''.$avatar_api_url.'/data/noavatar_'.$size. $image_type . '\'">';
	}
	else
	{
		return $returnsrc ? 
		$avatar_api_url.'/data/noavatar_'.$size. $image_type: 
		'<img '.$add_str.'src="'.$avatar_api_url.'/data/noavatar_'.$size. $image_type .'" onerror="this.onerror=null;this.src=\''.$avatar_api_url.'/data/noavatar_'.$size. $image_type . '\'">';
	}
}
    
// 得到头像
function passport_avatar_file($userid, $size, $real_path = FALSE, $show_path = FALSE)
{
    
    $avatar_api_dir = get_config_field('avatar_api_dir');
    $type = 'virtual';
    $userid = abs(intval($userid));
    $userid = sprintf("%09d", $userid);
    $dir1 = substr($userid, 0, 3);
    $dir2 = substr($userid, 3, 2);
    $dir3 = substr($userid, 5, 2);
    $typeadd = $type == 'real' ? '_real' : '';
    
    if($real_path)
    {
        if($show_path === TRUE)
        {
            return $avatar_api_dir.'data'.DS.'avatar'.DS.$dir1.DS.$dir2.DS.$dir3.DS;
        }
        else
        {
            return $avatar_api_dir.'data'.DS.'avatar'.DS.$dir1.DS.$dir2.DS.$dir3.DS.substr($userid, -2).$typeadd."_avatar_$size.jpg";
        }
    }
    else
    {
		return $dir1.DS.$dir2.DS.$dir3.DS.substr($userid, -2).$typeadd."_avatar_$size.jpg";
    }
}

// 会员中心用户日志
function passport_write_log($userid, $content, $return_data = '')
{
    $ip = ip();
    $time = date('Y-m-d H:i:s');
    $url = trim('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $url = arr_to_utf8($url); // 这个可能有GBK
    
    $server_addr = trim($_SERVER['SERVER_ADDR']);

    $data = array();
    $data['userid'] = $userid;
    $data['time'] = $time;
    $data['ip'] = $ip;
    
    /* 不记录POST日志
    if(count($_POST) > 0)
    {
        $str_post = '|POST:';
        foreach($_POST as $key => $value)
        {

            $str_post .= $key."=".$value.'&';
        }
        $str_post = substr($str_post, 0, strlen ($str_post) -1);
    }
    else
    {
        $str_post = '';
    }
    */
    
    $str_post = '';
    $str_post = arr_to_utf8($str_post);
    
    $data['url'] = $url;
    $data['content'] = $content.$str_post;
    $data['server_addr'] = $server_addr;
    $data['return_data'] = $return_data;

    $_CI =& get_instance();
    $_CI->load->model('member_model');
    if ($content != '') {
        $id = $_CI->member_model->setPassportLog($data);
        if($id + 0 > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    else
    {
        return FALSE;
    }
}

// 会员中心短信日志
function passport_write_sms_log($mobile, $content)
{
    $_CI =& get_instance();
    $_CI->load->library('sms');
    $ip = ip();
    $posttime = time();

    // 先检查同一个手机号码是否大于1分钟
    $data = array();
    $data['mobile'] = $mobile;
    $arr_log = $_CI->member_model->getPassportSmsLog($data);
    
    $last_post_time = (count($arr_log) > 0) ? ($arr_log[0]['posttime'] + 0) : 0;
    
    $pass_time = $posttime - $last_post_time;
    if($pass_time < 60)
    {
        return '请在60秒后再试';
    }
    
    $data = array();
    $data['mobile'] = $mobile + 0;
    $data['posttime'] = $posttime;
    $data['content'] = trim($content);
    $data['return_msg'] = $_CI->sms->msg_post($data['mobile'], $data['content']);
    $data['ip'] = $ip;
    $data['user_name'] = $_CI->sms->user_name;
    $data['money'] = $_CI->sms->find_money();  // 这个要大于30S调用1次

    $_CI->load->model('member_model');
    $id = $_CI->member_model->setPassportSmsLog($data);
    if($id + 0 > 0) {
        return TRUE;
    } else {
        return '记录失败，请稍候再试';
    }
}

// 会员中心用户注册
function passport_register($arr_input)
{
    $arr_return = array('success' => FALSE, 'message' => '', 'uc_msg' => '');   // 默认返回

    $_CI =& get_instance();
    // zhangjh 2015-01-16 用户中心注册多验证一些内容
    if($arr_input['from'] == 'passport')
    {
        if($arr_input['method'] == 'email')
        {
			if($arr_input['email'] == '')
			{
				$arr_return['message'] = 'Email没有填写';
			}
			elseif($arr_input['verify_code'] == '')
			{
				$arr_return['message'] = '验证码没有填写';
			}
            elseif($arr_input['verify_code'] != $_CI->session->get('verify_code'))
            {
                $arr_return['message'] = '验证码不正确';
            }
        }
        elseif($arr_input['method'] == 'mobile')
        {
            if($_CI->session->get('verify_sms_code') == '' && $arr_return['message'] == '')
            {
                $arr_return['message'] = '没有发送短信校验码';
            }
            
            if($arr_input['sms_verify'] == '' && $arr_return['message'] == '')
            {
                $arr_return['message'] = '没有输入短信校验码';
            }
            
            if($arr_input['sms_verify'] != $_CI->session->get('verify_sms_code') && $arr_return['message'] == '')
            {
                $arr_return['message'] = '短信验证码不正确';
            }
            
            if($arr_return['message'] == '')
            {
                $arr_input['email'] = 'p_'.$arr_input['mobile'].'@example.com'; // 暂定
            }
        }
    }
	elseif($arr_input['from'] == 'api')
	{
	}

    // 1、参数检查
    $_CI->load->model('member_model');
    if($arr_return['message'] == '')
    {
        $data = array('username' => $arr_input['username']);
        $row = $_CI->member_model->get($data);
        if(count($row) > 0)
        {
            $arr_return['message'] = '用户名已经存在';
        }
    }

    if($arr_return['message'] == '')
    {
        $data = array('email' => $arr_input['email']);
        $row = $_CI->member_model->get($data);
        $arr_return['message'] = count($row) > 0 ? 'Email已经存在' : '';
    }

    if($arr_return['message'] == '' && !is_username($arr_input['username']))
    {
        $arr_return['message'] = '用户名有误';
    }

    if($arr_return['message'] == '' && (is_badword($arr_input['password']) || !is_password($arr_input['password'])))
    {
        $arr_return['message'] = '密码有误';
    }

    if($arr_return['message'] == '' && $arr_input['password'] != $arr_input['pwdconfirm'])
    {
        $arr_return['message'] = '确认密码和密码不一致';
    }

    if($arr_return['message'] == '' && !is_email($arr_input['email']))
    {
        $arr_return['message'] = 'Email有误';
    }
	
	
	$arr_input['nickname'] =  is_gbk($arr_input['nickname']) ? arr_to_utf8($arr_input['nickname']) : $arr_input['nickname'];
	
	if($arr_return['message'] == '' && trim($arr_input['nickname']) == '')
    {
        $arr_return['message'] = '昵称没有填写';
		$arr_return['return_step'] = 2;
    }
	
	if($arr_return['message'] == '')
	{
		$row = $_CI->member_model->getMemberByNickName($arr_input['nickname']);
		if(is_object($row))
		{
			$arr_return['message'] = '昵称已经存在';
			$arr_return['return_step'] = 2;
		}
	}
	
	if($arr_return['message'] == '' && !is_username($arr_input['nickname']))
    {
        $arr_return['message'] = '昵称有误';
		$arr_return['return_step'] = 2;
    }
	
	$pass = passport_email_can_register($arr_input['email']);	// 是否可以注册
	if($pass['success'] === FALSE)
	{
		$arr_return['message'] = $pass['message'];
	}
	
	// 2、组合参数
    if($arr_return['message'] == '')
    {
        $userinfo = array();
        $userinfo['encrypt'] = create_randomstr(6);
        $userinfo['username'] = (isset($arr_input['username']) && is_username($arr_input['username'])) ? $arr_input['username'] : exit('2');
        $userinfo['nickname'] = (isset($arr_input['nickname']) && is_username($arr_input['nickname'])) ? $arr_input['nickname'] : exit('4');
        $userinfo['email'] = (isset($arr_input['email']) && is_email($arr_input['email'])) ? $arr_input['email'] : exit('1');
        $userinfo['password'] = (isset($arr_input['password']) && is_badword($arr_input['password'])==false) ? $arr_input['password'] : exit('3');
        $userinfo['modelid'] = isset($arr_input['modelid']) ? intval($arr_input['modelid']) : 10;
        $userinfo['regip'] = ip();
        $userinfo['point'] = 0;
        $userinfo['amount'] = 0;
        $userinfo['regdate'] = $userinfo['lastdate'] = time();
        $userinfo['siteid'] = 1;
        $userinfo['connectid'] = trim(@$arr_input['connectid']);
        $userinfo['from'] = trim(@$arr_input['from']);
		$userinfo['remote_avatar'] = trim(@$arr_input['remote_avatar']);
        $userinfo['mobile'] = isset($arr_input['mobile']) ? $arr_input['mobile'] : '';
    }

    // 3、注册 UC 会员
    if($arr_return['message'] == '')
    {
        $param = array();
        $param['action'] = 'set_synregister_uc';
		
		$userinfo['username'] = $arr_input['nickname'];	// UC的username用nickname 2015-03-11
        $param['userinfo'] = $userinfo;

        $_CI->load->library('api_out');
        $arr_register = $_CI->api_out->phpcms($param);

        $arr_return['uc_msg'] = $arr_register['message'];

        if($arr_register['success'] === TRUE) // UC注册成功
        {
            $ucuserid = $arr_register['ucuserid'] + 0;
        }
        else
        {
			$arr_return['message'] = 'UC注册失败|'.$arr_return['uc_msg'];

            // 内网通讯问题，不处理
            if($_CI->config->item('domain') == 'zhangjh.dev.usport.cc' || $_CI->config->item('domain') == 'usport.cc')
            {
                $ucuserid = date('His');
                $arr_return['message'] = '';
            }
        }
		$userinfo['username'] = $arr_input['username'];	// UC的username用nickname，用完就恢复
    }

    // 4、注册 phpsso 会员
    if($arr_return['message'] == '')
    {
        $param = array();
        $param = array( 'username' => $userinfo['username'],
                        'password' => md5(md5($userinfo['password']).$userinfo['encrypt']),
                        'email' => $userinfo['email'],
                        'regip' => $userinfo['regip'],
                        'regdate' => $userinfo['regdate'],
                        'lastdate' => $userinfo['lastdate'],
                        'appname' => 'phpcms v9',
                        'type' => 'app',
                        'random' => $userinfo['encrypt'],
                        'ucuserid' => $ucuserid);

        $uid = $_CI->member_model->setSsoMember($param);
        $uid = $uid + 0;
        if($uid > 0)    // phpsso 会员注册成功
        {}
        else    // -1 参数不足, -2 已经有账号
        {
             $arr_return['message'] = $uid == -1 ? 'SSO参数不足' : ($uid == -2 ? 'SSO已有账号' : 'SSO注册失败');
        }
    }

    // 5、注册 phpcms 会员
    if($arr_return['message'] == '')
    {
        $userinfo['phpssouid'] = $uid;
        $userinfo['password'] = md5(md5($userinfo['password']).$userinfo['encrypt']);
        unset($userinfo['mobile']);

        $userid = $_CI->member_model->setMember($userinfo);
        $userid = $userid + 0;
        if($userid > 0)    // phpcms 会员注册成功
        {
            if(trim(@$arr_input['mobile'] != '') || trim(@$arr_input['gender'] != ''))  // 如果有输入mobile或gender
            {
                // 插入 member_detail
                $data_detail = array();
                $data_detail['userid'] = $userid;
                $data_detail['mobile_no'] = isset($arr_input['mobile']) ? $arr_input['mobile'] : '';
				$data_detail['bind_mobile'] = '1';
                $data_detail['gender'] = isset($arr_input['gender']) ? $arr_input['gender'] : '';
                $data_detail['introduction'] = '';
                $_CI->member_model->setMemberDetail($data_detail);
            }
        }
        else    // -1 参数不足, -2 已经有账号
        {
             $arr_return['message'] = $userid == -1 ? 'CMS参数不足' : ($userid == -2 ? 'CMS已有账号' : 'CMS注册失败');
        }
    }

    if($arr_return['message'] == '')
    {
        $arr_return['success'] = TRUE;
        $arr_return['message'] = '注册成功';
        $arr_return['ucuserid'] = $ucuserid;        // UC会员ID
        $arr_return['uid'] = $uid;                  // SSO会员ID
        $arr_return['userid'] = $userid;            // 会员ID
        $arr_return['nickname'] = $userinfo['nickname'];

        // 发送Email
        if(($arr_input['from'] == 'passport' || $arr_input['from'] == 'api') && is_email($arr_input['email']))
        {
            if($arr_input['method'] == 'email')
            {
                $add_message = '邮箱注册';
                $email_result = '|'.passport_send_mail($arr_input['email'], 1);

            }
            elseif($arr_input['method'] == 'mobile')
            {
                $add_message = '手机注册';
                $email_result = '';
            }

            passport_write_log($userid, '用户注册|'.$arr_input['from'].'|'.$add_message, '注册成功'.$email_result);
            $arr_return['message'] .= '|'.$add_message;
        }
        else
        {
            // 写 log 日志
            passport_write_log($userid, '用户注册|passport|第三方注册', '注册成功');
        }
    }
	
	if($arr_return['success'] == FALSE)
	{
		if(@$arr_return['return_step'] != 2)
		{
			$arr_return['return_step'] = 1;
		}
	}
    return $arr_return;
}

// 发邮件功能
function passport_send_mail($to, $type, $confirm_url = '')
{
    $_CI =& get_instance();
    $confirm_url = ($confirm_url == '') ? $_CI->config->item('home_url') : $confirm_url;

    if($type == 1) // 邮箱注册发通知Email
    {
        // 注册账号用
        $subject = "5U体育感谢注册";
        $message = "尊敬的 ".$to."：<br><br>
感谢您注册5U体育!<br>
您的登录名为: ".$to."<br><br>
点击以下链接，开始5U体育之旅吧：<br>
<a href='".$confirm_url."' target='_blank'>".$confirm_url."</a><br><br>
如果以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。<br><br>
此为系统邮件请勿回复。<br><br>
5U体育管理员";
    }
    elseif($type == 2) // 找回密码发Email 暂时没有功能
    {
        $subject = "5U体育找回密码";
        $message = "尊敬的 ".$to."：<br><br>
您已经在5U体育提交找回密码的申请，请单击下方链接来进行修改密码：<br>
<a href='".$confirm_url."' target='_blank'>点击这里修改密码</a><br><br>
如果以上链接不能打开，请将下面地址复制到您的浏览器（如IE）的地址栏中，打开页面后同样可以完成修改密码。<br>
（该链接在 30分钟 内有效，如果超时请登录5U体育会员登录界面重新发送验证邮件）。<br>
".$confirm_url."<br><br>
您的电子邮箱是：".$to."<br>
确认电子邮箱能帮您更安全地保护账户。<br><br>
此为系统邮件请勿回复。<br><br>
5U体育管理员";
    }
    elseif($type == 3)  // Email绑定用
    {
        
        $subject = "5U体育绑定邮箱";
        $message = "尊敬的 ".$to."：<br><br>
您已经在5U体育申请绑定邮箱，请单击下方链接来进行确认：<br>
<a href='".$confirm_url."' target='_blank'>点击确认绑定该邮箱</a><br><br>
如果以上链接不能打开，请将下面地址复制到您的浏览器（如IE）的地址栏中，打开页面后同样可以完成绑定邮箱。<br>
".$confirm_url."<br><br>
您的电子邮箱是：".$to."<br>
绑定电子邮箱能帮您更安全地保护账户。<br><br>
此为系统邮件请勿回复。<br>
如果你错误地收到了此电子邮件，你无需执行任何操作！<br>
5U体育管理员";
    }
    elseif($type == 4)  // 找回密码用
    {
        $subject = "5U体育找回密码";
        $message = "尊敬的 ".$to."：<br><br>
您已经在5U体育申请找回密码，请单击下方链接来进行重置密码：<br>
<a href='".$confirm_url."' target='_blank'>点击重置密码</a><br><br>
如果以上链接不能打开，请将下面地址复制到您的浏览器（如IE）的地址栏中，打开页面后同样可以操作重置密码。<br>
".$confirm_url."<br><br>
您的电子邮箱是：".$to."<br>
绑定电子邮箱能帮您更安全地保护账户。<br><br>
此为系统邮件请勿回复。<br>
如果你错误地收到了此电子邮件，你无需执行任何操作！<br>
5U体育管理员";
    }
    else
    {
        return "Email类型错误";
    }

    $_CI->load->library('email');
    $_CI->email->from($_CI->email->smtp_user, '5U体育管理员');
    $_CI->email->to($to);
    $_CI->email->subject($subject);
    $_CI->email->message($message);

    if(!$_CI->email->send())
    {
        return "发送失败";
    }
    else
    {
        return "发送成功";
    }
}

// 生成Eamil操作URL
function passport_mail_option_url($data)
{
    $url_key = 'AIzlUt97bmJHjZJ6XpSK';
    $confirm_url = '';
    
    $time = time();
    $action = trim($data['action']);
    $url_head = trim($data['url_head']);
    
    if($action == 'bind_email' || $action == 'change_email' || 
       $action == 'reset_password' || $action == 'reset_password2') // 绑定Email或重置密码
    {
        $param = array();
        $param['action'] = $action;
        $param['userid'] = @$data['userid'] + 0;
        $param['username'] = trim(@$data['username']);
        $param['new_pwd'] = trim(@$data['new_pwd']);
        $param['from'] = trim(@$data['from']);
        $param['email'] = trim(@$data['email']);
		$param['mobile'] = trim(@$data['mobile']);
		$param['find_type'] = trim(@$data['find_type']);
        $param['time'] = $time;
        $json_data = json_encode($param);
        $code_data = urlencode(decode($json_data, 'E', $url_key));
        $sign = md5(md5('mailurl'.$action.$time.$url_key));
        $confirm_url = $url_head.'?action='.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
    }
    return $confirm_url;
}

// 修改UC的Email
function passport_change_uc_email($phpssouid, $email)
{
    $_CI =& get_instance();
    $_CI->load->model('member_model');
    
    $phpssouser = $_CI->member_model->getSsoMember($phpssouid);
    $ucuserid = $phpssouser->ucuserid + 0;
    
    // 修改UC的Email 
    $userinfo = array();
    $userinfo['uid'] = $ucuserid;       // UC的用户ID
    $userinfo['email'] = $email;
    $param = array();
    $param['action'] = 'set_synupdate_uc';
    $param['userinfo'] = $userinfo;

    $_CI->load->library('api_out');
    $arr_update = $_CI->api_out->phpcms($param);
    $uc_msg = $arr_update['message'];
    
    if($uc_msg != 'uc修改成功')
    {
        return $uc_msg;
    }
    else
    {
        return TRUE;
    }
}

// 修改用户Email
function passport_bind_email($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');   // 默认返回
    
    $_CI =& get_instance();
    $_CI->load->model('member_model');
    
    $data['userid'] = $data['userid'] + 0;
    
    if($arr_return['message'] == '')
    {
        $param = array();
        $param = array('userid' => $data['userid']);
        $row = $_CI->member_model->get($param);
		
        if(count($row) == 0)
        {
            $arr_return['message'] = '用户名不存在';
			$old_email = '';
			$phpssouid = 0;
        }
		else
		{
			$old_email = $row['email'];
			$phpssouid = $row['phpssouid'] + 0;
		}
    }
    
    if($arr_return['message'] == '')
    {
        $param = array();
        $param = array('email' => $data['email']);
        $row_email = $_CI->member_model->get($param);
        
        if(count($row_email) > 0) // 有这个Email记录记录
        {
            $user_detail = $_CI->member_model->getMemberDetail($data['userid']);
            
            if(is_object($user_detail)) // 有记录
            {
                if($row_email['userid'] == $data['userid'] && $user_detail->bind_email == 1)
                {
                    $arr_return['message'] = '已经被您绑定了，请不要重复操作';
                }
                elseif($row_email['userid'] == $data['userid'] && $user_detail->bind_email != 1)
                {
                    $mix_change_uc_email = passport_change_uc_email($phpssouid, $data['email']);
                    if($mix_change_uc_email === TRUE)
                    {
                        $param = array();
                        $param['bind_email'] = 1;
                        $_CI->member_model->editMemberDetail($data['userid'], $param);
                        
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '绑定成功';
                    }
                    else
                    {
                        $arr_return['message'] = $mix_change_uc_email;
                    }
                }
                elseif($row_email['userid'] != $data['userid'] && $user_detail->bind_email == 1)
                {
                    $arr_return['message'] = '这个Email已绑定其他账号';
                }
                elseif($row_email['userid'] != $data['userid'] && $user_detail->bind_email != 1)
                {
                    $arr_return['message'] = '这个Email属于其他账号，但是没有绑定';
                }
            }
            else
            {
                if($row_email['userid'] == $data['userid'])
                {
                    $mix_change_uc_email = passport_change_uc_email($phpssouid, $data['email']);
                    if($mix_change_uc_email === TRUE)
                    {
                        $param = array();
                        $param['userid'] = $data['userid'];
                        $param['bind_email'] = 1;
                        $_CI->member_model->setMemberDetail($param);
                        
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '新写绑定记录，绑定成功';
                    }
                    else
                    {
                        $arr_return['message'] = $mix_change_uc_email;
                    }
                }
                else
                {
                    $arr_return['message'] = '这个Email属于其他账号，但是没有绑定';
                }
            }
        }
        else
        {
            $mix_change_uc_email = passport_change_uc_email($phpssouid, $data['email']);
            if($mix_change_uc_email === TRUE)
            {
                $param = array();
                $param['email'] = $data['email'];
                $_CI->member_model->editMember($data['userid'], $param);
                
                $user_detail = $_CI->member_model->getMemberDetail($data['userid']);
                
                if(is_object($user_detail)) // 有记录
                {
                    if($user_detail->bind_email == 1)
                    {
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '绑定成功';
                    }
                    elseif($user_detail->bind_email != 1)
                    {
                        $param = array();
                        $param['bind_email'] = 1;
                        $_CI->member_model->editMemberDetail($data['userid'], $param);
                        
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '绑定成功';
                    }
                }
                else
                {
                    $param = array();
                    $param['userid'] = $data['userid'];
                    $param['bind_email'] = 1;
                    $_CI->member_model->setMemberDetail($param);
                    
                    $arr_return['success'] = TRUE;
                    $arr_return['message'] = '新写绑定记录，绑定成功';
                }
            }
            else
            {
                $arr_return['message'] = $mix_change_uc_email;
            }
        }
    }
    
    if($arr_return['success'] == TRUE)
    {
        if($data['action'] == 'bind_email')
            passport_write_log($data['userid'], '用户邮箱绑定|passport|email:'.$old_email.'->'.$data['email'], '绑定成功');
        elseif($data['action'] == 'change_email')
            passport_write_log($data['userid'], '用户邮箱修改|passport|email:'.$old_email.'->'.$data['email'], '修改成功');
    }
    else
    {
        if($data['action'] == 'bind_email')
            passport_write_log($data['userid'], '用户邮箱绑定|passport|email:'.$old_email.'->'.$data['email'], '绑定失败|'.$arr_return['message']);
        elseif($data['action'] == 'change_email')
            passport_write_log($data['userid'], '用户邮箱修改|passport|email:'.$old_email.'->'.$data['email'], '修改失败|'.$arr_return['message']);
    }
    
    return $arr_return;
}

// 确定Eamil操作
function passport_mail_option($data)
{
    $arr_return = array('success' => FALSE, 'message' => 'init');
    $url_key = 'AIzlUt97bmJHjZJ6XpSK';
    
    $action = $data['action'];
    $code_data = $data['code_data'];
    $time = trim($data['time']) + 0;
    $sign = trim($data['sign']);

    $_tmp_sign = 'mailurl';
    foreach($data as $k => $v)
    {
        if($k != 'sign' && $k != 'code_data' && $k != 'new_pwd')
        {
            $_tmp_sign .= $v;
        }
    }
    
    $my_sign = md5(md5($_tmp_sign.$url_key));
    $my_time = time();

    if(count($data) != 4 && count($data) != 5)  // 要有4、5个参数
    {
        $arr_return = array('success' => FALSE, 'message' => 'parameter_error');
    }
    elseif($my_time - $time > 3600 && FALSE)    // 1H内有效 不检查timeout
    {
        $arr_return = array('success' => FALSE, 'message' => 'timeout');
    }
    elseif($my_sign !== $sign)  // 验证是否正确
    {
        $arr_return = array('success' => FALSE, 'message' => 'sign_error');
    }
    else
    {
        $arr_return = array('success' => TRUE, 'message' => 'ok');
        if($action == 'bind_email' || $action == 'change_email')
        {
            $json_data = decode($code_data, 'D', $url_key); // 解密
            $arr_user = json_decode($json_data, TRUE);
            $arr_return = passport_bind_email($arr_user);
        }
        elseif($action == 'reset_password' || $action == 'reset_password2') // 重置密码的第一步和第二步
        {
            $json_data = decode($code_data, 'D', $url_key); // 解密
            $arr_user = json_decode($json_data, TRUE);
            $_CI =& get_instance();
            $_CI->load->model('member_model');
            
			if($arr_user['find_type'] == 'email' || trim($arr_user['email']) != '') // Email找回
			{
				if(trim($arr_user['username']) == '')
				{
					$param = array();
					$param['email'] = trim($arr_user['email']);
				}
				else
				{
					$param = array();
					$param['username'] = trim($arr_user['username']);
				}
				$member = $_CI->member_model->get($param);
			}
			elseif($arr_user['find_type'] == 'mobile') // 手机找回
			{
				$param = array();
				$param['all_search'] = 1;
				$param['mobile'] = trim($arr_user['mobile']);
				$member = $_CI->member_model->getMemberByMobile($param);
			}
			
            if(count($member) > 0)   // 有这个账号
            {
                $user_detail = $_CI->member_model->getMemberDetail($member['userid']);
                if(is_object($user_detail))
                {
                    if($user_detail->apply_reset_password == 1) // 有申请的找回密码的
                    {
                        if($action == 'reset_password') // 重置密码的第一步
                        {
                            // 生成参数
                            $param = array();
                            $param['action'] = 'reset_password2';
                            $param['url_head'] = $_CI->config->item('home_url').'/passport/email/confirm';
                            $param['username'] = trim($arr_user['username']);
                            $param['email'] = trim($arr_user['email']);
							$param['mobile'] = trim($arr_user['mobile']);
							$param['find_type'] = trim($arr_user['find_type']);
                            $param['from'] = trim($arr_user['from']);
                            $return = passport_mail_option_url($param);
                            
                            $arr_form = parse_url($return);
                            $arr_param = explode('&', $arr_form['query']);
                            
                            $str_form = "<form id='form_pwd' method='post' target='_self' action='".$param['url_head']."'>\n";
                            foreach($arr_param as $key => $value)
                            {
                                $_tmp = explode('=', $value);
                                $str_form .= "<input type='hidden' id='".$_tmp[0]."' name='".$_tmp[0]."' value='".$_tmp[1]."'>\n";
                            }
                            $str_form .= "新密码：<input type='password' autocorrect='off' id='new_pwd' name='new_pwd' value=''>&nbsp;&nbsp;";
                            $str_form .= "<input type='submit' id='btn_smt' value='提交'><br><br>";
                            $str_form .= "密码长度8~16位，数字、字母、字符至少包含两种";
                            $str_form .= "</form>";
                            echo $str_form;
                        }
                        else // 重置密码的第二步
                        {
                            $param = array();
                            $param['userid'] = $member['userid'];
                            $param['from'] = trim($arr_user['from']);
                            $param['new_pwd'] = $data['new_pwd'];
                            $param['reset_password'] = 1;
    
                            $return = passport_change_password($param);
                            if($return['success'] === TRUE) // 修改成功，修改 apply_reset_password 为 0
                            {
                                $param = array();
                                $param['apply_reset_password'] = 0;
                                $_CI->member_model->editMemberDetail($member['userid'], $param);
                            }
							die($return['message'].'，请使用新密码登录<script>setTimeout(function(){ parent.window.location.href = "'.trim($_CI->config->item('home_url')).'/member/"; }, 3000);</script>');
                        }
                    }
                    else
                    {
						die('没有要处理的，请登录<script>setTimeout(function(){ parent.window.location.href = "'.trim($_CI->config->item('home_url')).'/member/"; }, 3000);</script>');
                    }
                }
            }
            die();  // 终止
        }
        else
        {
            $arr_return = array('success' => FALSE, 'message' => '没有对应的操作类型');
        }
    }

    return $arr_return;
}

// 修改用户密码
function passport_change_password($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $userid = trim($data['userid']) + 0;
    $from = trim($data['from']);
    $old_pwd = @$data['old_pwd'];   // 找回密码这个可能没有
    $new_pwd = $data['new_pwd'];
    $new_pwd_confirm = @$data['new_pwd_confirm'];   // 这个API可能没有，找回密码可以也没有
    $reset_password = @$data['reset_password'] + 0; // 是否是重置密码的请求
    
    if($from == '')
    {
        $arr_return['message'] = '来源错误';
    }
    
    if($new_pwd != $new_pwd_confirm && $from == 'passport' && $arr_return['message'] == '' && $reset_password == 0)
    {
        $arr_return['message'] = '两次密码输入不相同';
    }
    
    if($old_pwd == '' && $arr_return['message'] == '' && $reset_password == 0)
    {
        $arr_return['message'] = '没有输入旧密码';
    }
    
    if($new_pwd == '' && $arr_return['message'] == '')
    {
        $arr_return['message'] = '没有输入新密码';
    }
    
    if($new_pwd_confirm == '' && $arr_return['message'] == '' && $from == 'passport' && $reset_password == 0)
    {
        $arr_return['message'] = '没有输入确认密码';
    }
    
    if(!is_password($new_pwd) && $arr_return['message'] == '')
    {
        $arr_return['message'] = '新密码格式有误';
    }
    
    if($arr_return['message'] == '')
    {
        $_CI =& get_instance();
        $_CI->load->model('member_model');
        $old = $_CI->member_model->getMember($userid);
        $old = (object)$old;
        if(is_object($old))
        {
            $md5_password = md5(md5($old_pwd).$old->encrypt);
            
            if($old->password == $md5_password || $reset_password == 1) // 验证成功
            {
                $phpssouid = $old->phpssouid + 0;
                $phpssouser = $_CI->member_model->getSsoMember($phpssouid);
                $ucuserid = $phpssouser->ucuserid + 0;
                
                // 处理UC修改密码
                $userinfo = array();
                $userinfo['uid'] = $ucuserid;       // UC的用户ID
                $userinfo['password'] = $new_pwd;   // UC修改传明码
                $param = array();
                $param['action'] = 'set_synupdate_uc';
                $param['userinfo'] = $userinfo;
                
                $_CI->load->library('api_out');
                $arr_update = $_CI->api_out->phpcms($param);
                $uc_msg = $arr_update['message'];
                if($uc_msg != 'uc修改成功')
                {
                    $arr_return['message'] = $uc_msg;
                }
                
                if($arr_return['message'] == '')
                {
                    $phpssouid = $old->phpssouid + 0;
                    $param = array();
                    $param = array('password' => md5(md5($new_pwd).$old->encrypt));
                    $_CI->member_model->editSsoMember($phpssouid, $param);
                    
                    $param = array();
                    $param = array('password' => md5(md5($new_pwd).$old->encrypt));
                    $_CI->member_model->editMember($userid, $param);
                    
                    $arr_return['message'] = '修改成功';
                    $arr_return['success'] = TRUE;
                    
                    passport_write_log($userid, '用户修改密码|'.$from, '修改成功');
                }
                else
                {
                    passport_write_log($userid, '用户修改密码|'.$from, '修改失败|'.$arr_return['message']);
                }
            }
            else
            {
                $arr_return['message'] = '旧密码不正确';
            }

        }
        else
        {
            $arr_return = array('success' => FALSE, 'message' => '数据出错');
        }
    }
    return $arr_return;
}

// 用户找回密码(email/mobile)
function passport_find_password($data, $bln_only_email = FALSE)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $username = trim(@$data['username']);
    $email = trim(@$data['email']);
	$mobile = trim(@$data['mobile']);
    $from = trim($data['from']);
	
	if($email != '') // email找回
	{
		if(!is_email($email))
		{
			$arr_return['message'] = '不是一个有效的Email地址';
		}
		else
		{
			$find_type = 'email';
			$find_by_tips = '邮箱';
		}
	}
	elseif($mobile != '')
	{
		if(!is_mobile($mobile))
		{
			$arr_return['message'] = '不是一个有效手机号码';
		}
		else
		{
			$find_type = 'mobile';
			$find_by_tips = '手机';
		}
	}
	else
	{
		$arr_return['message'] = '找回类型错误';
	}
	
	if($arr_return['message'] == '')
    {
        $_CI =& get_instance();
        $_CI->load->model('member_model');
        
		if($find_type == 'email')
		{
			if($bln_only_email === TRUE)
			{
				$param = array();
				$param['email'] = $email;
				$member = $_CI->member_model->get($param);
			}
			else
			{
				$param = array();
				$param['username'] = $username; // 这个参数可能没有
				$member = $_CI->member_model->get($param);
			}
		}
		elseif($find_type == 'mobile')
		{
			$param = array();
			$param['all_search'] = 1;
			$param['mobile'] = $mobile;
			$member = $_CI->member_model->getMemberByMobile($param);
		}
		
        if(count($member)> 0)   // 有这个账号
        {
            $user_detail = $_CI->member_model->getMemberDetail($member['userid']);
            if(is_object($user_detail))
            {
                if($user_detail->bind_email + 0 == 1 && $find_type == 'email')	// 有绑定邮箱
                {
                    $add_message = '|email:'.$email;
                            
                    // 取发邮件用的确认URL
                    $param = array();
                    $param['action'] = 'reset_password';
                    $param['url_head'] = $_CI->config->item('home_url').'/passport/email/confirm';
                    $param['username'] = $username;
                    $param['email'] = $email;
					$param['find_type'] = $find_type;
                    $param['from'] = $from;
        
                    $confirm_url = passport_mail_option_url($param);
                    $email_result = passport_send_mail($data['email'], 4, $confirm_url);
                    
                    // 修改一个字段 getMemberDetail
                    $param = array();
                    $param['apply_reset_password'] = 1;
                    $_CI->member_model->editMemberDetail($member['userid'], $param);
                    
                    passport_write_log($member['userid'], '用户邮箱重置密码申请|'.$from.$add_message, $email_result);
                    $arr_return = array('success' => TRUE, 'message' => '邮件已发送，请确认');
                }
				elseif($user_detail->bind_mobile + 0 == 1 && $find_type == 'mobile')
				{
					$add_message = '|mobile:'.$mobile;
					
					$param = array();
                    $param['action'] = 'reset_password';
                    $param['url_head'] = $_CI->config->item('home_url').'/passport/email/confirm';
                    $param['mobile'] = $mobile;
					$param['find_type'] = $find_type;
                    $param['from'] = $from;
					$confirm_url = urlencode(passport_mail_option_url($param));
					
					$param = array();
					$param['apply_reset_password'] = 1;
					$_CI->member_model->editMemberDetail($member['userid'], $param);
					passport_write_log($member['userid'], '用户手机重置密码申请|'.$from.$add_message, '');
					$arr_return = array('success' => TRUE, 'message' => '申请成功，请处理', 'confirm_url' => $confirm_url);
				}
                else
                {
                    $arr_return['message'] = '没有绑定'.$find_by_tips;
                }
            }
            else
            {
                $arr_return['message'] = '没有绑定'.$find_by_tips.'记录';
            }
        }
        else
        {
            $arr_return['message'] = '没有信息';
        }
    }
    return $arr_return;
}

// 生成头像
function new_passport_avatar_create($userid, $tmp_file, $type)
{
        // 先建文件夹
        $have_exist = passport_avatar_exist($userid);
        if(!$have_exist)
        {
            $upload_dir = get_config_field('avatar_api_dir');
            passport_avatar_home_set($userid, $upload_dir.'data'.DS.'avatar');
        }
        
        $big_file = passport_avatar_file($userid, 'big', TRUE, FALSE);
        $middle_file = passport_avatar_file($userid, 'middle', TRUE, FALSE);
        $small_file = passport_avatar_file($userid, 'small', TRUE, FALSE);
        
        include_once(BASEPATH.'../application/libraries/avatar/ImageCore.func.php'); // 图像缩放
        $func = '';
        $message = FALSE;
        
        switch ($type)
        {
            case 'gif':
                $func = 'imagecreatefromgif';
                $message = TRUE;
                break;
            
            case 'png':
                $func = 'imagecreatefrompng';
                $message = TRUE;
                break; 
            
            case 'jpeg':
            case 'jpg':
                $func = 'imagecreatefromjpeg';
                $message = TRUE;
                break; 
            default :
                $message = '错误的类型';
                break;;
        } 
        
        if($message === true)
        {
            $tmp_file = APPPATH . '../' . $tmp_file;
            $image = $func($tmp_file);
            imagejpeg($image, $big_file);
            @vxResize($big_file, $big_file, 200, 200);
            @vxResize($big_file, $middle_file, 120, 120);
            @vxResize($big_file, $small_file, 48, 48);
            @unlink($tmp_file);
        }
        
	return $message;
}



function get_avatar_api_url()
{
    $avatar_api_url = './upload/head/data/avatar';
    return $avatar_api_url;
}