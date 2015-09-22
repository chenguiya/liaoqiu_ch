<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
    /**
     * 构造函数
     */
    public $mem;
    public function __construct()
    {
        parent::__construct();
    }
	
	/**   
	 * [聊球相关Api]
	 * 取账号信息		http://zhangjh.dev.usport.cc/api/liaoqiu/get_member?userid=2714,2499&sign=19ca28c748c9383e1752d5b8b1899e08
	 * 注册账号			http://zhangjh.dev.usport.cc/api/liaoqiu/register_member?email=nsdone%40163.com&head=&sex=1&username=nsdone&password=123456dd&sign=5d0c88b36390d43ea7fe6002cb236ec6
	 * 取赛程列表		http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_list?client_type=0&league_id=1&top_id=2014-12-22&page_size=14&sign=09638bb4b1bf41010e2f8ecfbce6c886
	 * 取赛程列表(聊球)	http://zhangjh.dev.usport.cc/api/liaoqiu/get_lq_match_list?client_type=0&top_id=2014-12-22&page_size=14&sign=81c419b9717387a39818d6d721cd2c0a
	 * 账号登录			http://zhangjh.dev.usport.cc/api/liaoqiu/member_login?username=nsdone@163.com&password=123456dd&sign=d0a288182d0d36d1733cade952d54630
	 * 取积分射手榜		http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_integral_shooter?league_id=1&sign=ea337dd30877da37cb6ef66d02e6205e
	 * 积分射手更新		http://zhangjh.dev.usport.cc/event/grab_data
	 * 获取联赛列表		http://zhangjh.dev.usport.cc/api/liaoqiu/get_league_list?event_id=1&sign=1a17e748509651cbfe8a9c8ea6c7f10a
	 * 获取球队列表		http://zhangjh.dev.usport.cc/api/liaoqiu/get_team_list?league_id=1&sign=ea337dd30877da37cb6ef66d02e6205e
	 * 获取球队详细		http://zhangjh.dev.usport.cc/api/liaoqiu/get_team_detail?team_id=24&sign=18cbab5284b1015d66a0bc4e7666c09a
	 * 获取神评文章		http://zhangjh.dev.usport.cc/api/liaoqiu/get_god_comment?match_id=41&sign=2d1ab953d2e21bfcea8143a45f1de0d2
	 * 获取新闻列表		http://zhangjh.dev.usport.cc/api/liaoqiu/get_news_list?match_id=41&sign=2d1ab953d2e21bfcea8143a45f1de0d2
	 * 获取新闻详情		http://zhangjh.dev.usport.cc/api/liaoqiu/get_news_detail?news_id=81379&sign=28df751595f7ba7fcf2ce4d168b3ec6c
	 * 显示文章2		http://zhangjh.dev.usport.cc/api/liaoqiu/get_news_detail2?news_id=81382&sign=2d1ab953d2e21bfcea8143a45f1de0d2
	 * 修改会员资料		http://zhangjh.dev.usport.cc/api/liaoqiu/edit_member_info?userid=2499&email=&nickname=&sex=2&address=&signiture=&sign=ca27a49a8dfc76a1fa5fee4c5f270a32
						http://zhangjh.dev.usport.cc/api/liaoqiu/edit_member_info?userid=2499&email=&nickname=&sex=1&address=&signiture=&sign=015b90e31d394acc0f34b68f2a41b464
						http://zhangjh.dev.usport.cc/api/liaoqiu/edit_member_info?userid=2835&email=&nickname=hashita&sex=1&address=&signiture=这个是什么&sign=f1c3ae20633a0e03bb620d3662f5cae5
	 * 搜索账号接口		http://zhangjh.dev.usport.cc/api/liaoqiu/search_member?age_min=18&age_max=28&sex=2&username=&sign=a9d84bbc013fc4ec2f466a17fdb95ffd
						http://zhangjh.dev.usport.cc/api/liaoqiu/search_member?age_min=&age_max=&sex=&username=libo&sign=31ead8a23b050446331b43cf838c7d33
	 * 修改密码接口		http://zhangjh.dev.usport.cc/api/liaoqiu/change_password?userid=2835&oldpwd=123456dd&newpwd=123456cc&sign=d7c871f4104cd1179779934c3efd98f7
						http://zhangjh.dev.usport.cc/api/liaoqiu/change_password?userid=2835&oldpwd=123456cc&newpwd=123456dd&sign=02c935ec0c35cad0fcc4c0d74343a665
	 * 找回密码			http://zhangjh.dev.usport.cc/api/liaoqiu/find_password?account=nsdone@163.com&email=nsdone@163.com&sign=47a5cc73a76d1f195dd2ec76499f937f
	 * 绑定邮箱			http://zhangjh.dev.usport.cc/api/liaoqiu/bind_email?account=nsdone@163.com&email=nsdone@163.com&sign=47a5cc73a76d1f195dd2ec76499f937f
	 * 设置头像			http://zhangjh.dev.usport.cc/api/liaoqiu/edit_member_avatar?userid=2499&input=&sign=ddd
	 * 查看日志			http://zhangjh.dev.usport.cc/api/liaoqiu/watch_file_log?type=edit_member_avatar&sign=9b4c2971172a09d98206f8fac9c82d7c
	 
	 * 比赛技术统计、比赛事件接口	http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_data?match_id=987334&sign=f9aeba0cf4641624189e130e9950d9b7
	 * 批量取比赛比分和比赛状态		http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_result?match_id=2222,1816,2208,2226&sign=14abddf217c82e763bba17d1cc73461b
	 * 邮箱注册|第一步 http://zhangjh.dev.usport.cc/api/liaoqiu/register_email_1?email=nadone@163.com&password=123456dd&sign=b87cf1b8eebc1a3f8ba29a12e19efa88
	 * 邮箱注册|第二步 http://zhangjh.dev.usport.cc/api/liaoqiu/register_step_2?token=54e699c5b892d96b9eaf9b4c91471fc4&nickname=%E7%88%B1%E6%96%B0%E8%A7%89%E7%BD%97&sign=382ceb8f59ad68263bd24ceeecf47043
	 * 手机注册|生成验证码 http://zhangjh.dev.usport.cc/api/liaoqiu/send_phone_verify?phone=13560838447&type=1&timestamp=1426240608&sign=b53c6b052883be942fb133ae8beca6f1
	 * 手机注册|验证验证码(删) http://zhangjh.dev.usport.cc/api/liaoqiu/verify_phone?token=a0e0d80532404ea90fcd115a8b65f76a&verify_code=241540&timestamp=1426240608&sign=6c4df6bd79e5ca7680b6c830259f6307
	 * 手机注册|填写密码验证码(改) http://zhangjh.dev.usport.cc/api/liaoqiu/register_mobile_1?token=a0e0d80532404ea90fcd115a8b65f76a&verify_code=241540password=123456dd&sign=283e29b676aae6965697f292ffcc13e4
	 * 手机注册|填写昵称并注册 http://zhangjh.dev.usport.cc/api/liaoqiu/register_step_2?token=a0e0d80532404ea90fcd115a8b65f76a&nickname=%E9%99%88%E8%BF%91%E5%8D%97&sign=7cf0da935267411222c6af05fdb4346f
	
	 * 邮箱找回密码| http://zhangjh.dev.usport.cc/api/liaoqiu/resetpass_email?email=shouso@qq.com&sign=baaa557864ff0c8581af153f8376aaad
	 * 手机找回密码|生成验证码 http://zhangjh.dev.usport.cc/api/liaoqiu/send_phone_verify?phone=13560838447&type=2&timestamp=1426240608&sign=65be379a979d25de554713a13f3c567b
	 *              验证验证码 http://zhangjh.dev.usport.cc/api/liaoqiu/verify_phone?token=6ece1cdbf1cf5892993fcbd5f14e7403&verify_code=614004&timestamp=1426240608&sign=65be379a979d25de554713a13f3c567b
	 *              修改密码   http://zhangjh.dev.usport.cc/api/liaoqiu/resetpass_phone?token=6ece1cdbf1cf5892993fcbd5f14e7403&newpwd=123456kk&sign=81a2496bac2c51b33ac5f73801597c6f
	 * 绑定新邮箱|http://zhangjh.dev.usport.cc/api/liaoqiu/bind_email?userid=2946&password=123456&email=shouso@qq&sign=b8a1356de4f4ef2c8fe576a3d8f20647
				  http://zhangjh.dev.usport.cc/api/liaoqiu/bind_email2?userid=2946&email=shouso@qq&sign=b8a1356de4f4ef2c8fe576a3d8f20647
	 * 绑定新手机|生成验证码 http://zhangjh.dev.usport.cc/api/liaoqiu/send_phone_verify?phone=13560838447&type=4&timestamp=1426240608&sign=976e770b1a1096de4ca697f1d9694c07
	              验证验证码 http://zhangjh.dev.usport.cc/api/liaoqiu/verify_phone?token=dc222c50ea12117c313b587e73f6d7cc&verify_code=915412&timestamp=1426240608&sign=5e5952a4144b617723f5f8b570e40e05
	              绑定新手机(验证手机和修改手机一并完成) 
	              http://zhangjh.dev.usport.cc/api/liaoqiu/bind_phone?userid=2946&token=dc222c50ea12117c313b587e73f6d7cc&password=123456pp&verify_code=915412&sign=xxx
				  http://zhangjh.dev.usport.cc/api/liaoqiu/bind_phone2?userid=2946&token=dc222c50ea12117c313b587e73f6d7cc&verify_code=915412&sign=xxx
	 第三方邮箱注册
	 http://zhangjh.dev.usport.cc/api/liaoqiu/register_oauth?type=1&email=nadone@163.com&password=123456dd&from=qq&connectid=2AD28C5987B6C26EDC64A509F65DFF11&remote_avatar=http%3A%2F%2Fq.qlogo.cn%2Fqqapp%2F100458914%2F2AD28C5987B6C26EDC64A509F65DFF&sign=587aeccd5c6a9003ff006540c563cf84
	 第三方手机注册
	 http://zhangjh.dev.usport.cc/api/liaoqiu/send_phone_verify?phone=13560838447&type=1&timestamp=1426240608&sign=b53c6b052883be942fb133ae8beca6f1
	 http://zhangjh.dev.usport.cc/api/liaoqiu/verify_phone?token=058921ed77aecc1f17207a0939b08eeb&verify_code=888776&timestamp=1426240608&sign=a64a61d3688c6a2bf03e6deda4c0f4e1
	 http://zhangjh.dev.usport.cc/api/liaoqiu/register_oauth?type=2&token=058921ed77aecc1f17207a0939b08eeb&password=123456xx&from=qq&connectid=92F68EBE6489E197A5FCC5706AFE4D93&remote_avatar=http%3A%2F%2Fq.qlogo.cn%2Fqqapp%2F100458914%2F2AD28C5987B6C26EDC64A509F65DFF&sign=xxx
	 第三方登录
	 http://zhangjh.dev.usport.cc/api/liaoqiu/login_oauth?from=qq&connectid=92F68EBE6489E197A5FCC5706AFE4D93&sign=6ce189db0969e0c54eee2087ecd1de72
	 
	 * 返回 state_code 0 成功
     *                 1 没有信息
     *                 2 sign验证错误
     *                 3 参数错误
     *                 4 参数不足
     *                 5 注册失败
     *                 6 账号或密码错误
     *                 7 没有要修改的内容
     *                 8 操作失败
     *                 9 用户已存在
     */
    public function liaoqiu($action, $sub_action = '')
    {
    	
		if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE)
		{
			$sn_key = '3#u29As9Fj23';
		}
		else
		{
			$sn_key = 'a@39e8a53Qs';    // 测试key:a@39e8a53Qs 正式key:3#u29As9Fj23
		}
        $arr_return = array();
        $arr_sign = array();
        $arr_param = array();
        
        $your_sign = '';
        
        // 处理参数
        parse_str($_SERVER['QUERY_STRING'], $_GET); // 参数可以支持中文
        
        $arr_get = is_array($this->input->get()) ? $this->input->get() : array();
        $arr_post = is_array($this->input->post()) ? $this->input->post() : array();
        $arr_request = array_merge($arr_get, $arr_post);
		$message = '';	// 记录ErrorLog
        foreach($arr_request as $key => $value)
        {
            $value = trim($this->input->get($key, TRUE)) == '' ? trim($this->input->post($key, TRUE)) : trim($this->input->get($key, TRUE));
            $arr_request[$key] = $value;
            $arr_param[$key] = urldecode($value);
            
            if($key == 'sign')
            {
                $your_sign = trim($value);
            }
            else
            {
                $arr_sign[] = $key.'='.urldecode($value);
            }
			$message .= $key.'='.urldecode($value).'|';
        }
		//write_file_log($message, 'liaoqiu', 'log_error', FALSE);
		
		// JAVA时间戳长度是13位，如：1294890876859 
		// PHP时间戳长度是10位， 如：1294890859
		$timestamp = trim(@$arr_request['timestamp']) + 0;
		//$timestamp = -1;	// 不检查
		if($timestamp > 0)
		{
			if(strlen($timestamp) == 13)
			{
				$timestamp = substr($timestamp, 0, 10);
			}
			$arr_request['timestamp'] = $timestamp;
		}
		
		$time = strtotime(date('Y-m-d H:i:s', $timestamp));
		$my_time = time();
		if(count($arr_sign) == 0 || $your_sign == '')
		{
			$arr_return['state_code'] = '4';
			$arr_return['state_desc'] = '参数不足';
		}
		elseif((abs($my_time - $time) > 3600) && $timestamp > 0) // 检查 timestamp 1H内有效
		{
			if($my_time - $time > 3600)
			{
				$arr_return['state_code'] = '3';
				$arr_return['state_desc'] = '参数错误|时间超时1小时';
			}
			else
			{
				$arr_return['state_code'] = '3';
				$arr_return['state_desc'] = '参数错误|时间超前1小时';
			}
		}
        else
        {
            asort($arr_sign);
            $str_sign = implode('&', $arr_sign).'||'.$sn_key;
            
            $my_sign = md5($str_sign);
            
            if($your_sign === $my_sign)
            {
				// $action 转换
				if($action == 'register_step_2')
				{
					$_token = trim(@$arr_param['token']);
					$_row = passport_token_to_type($_token);
					if(count($_row) == 1)
					{
						if($_row[0]['type'] == 'email')
						{
							$action = 'register_email_2';
						}
						elseif($_row[0]['type'] == 'mobile')
						{
							$action = 'register_mobile_2';
						}
					}
				}
				
                switch($action)
                {
					case 'watch_file_log':
					{
						$arr_return = $this->_watch_file_log($arr_param);
						break;
					}
					
                    case 'get_member':
                    {
                    	if(!$this->mem->get('arr_return')){
                    		 $arr_return = $this->_get_member($arr_param);
                    		 $this->mem->set('arr_return', $arr_return, 0, 3600);
                    	}else{
                    		$arr_return = $this->mem->get('arr_return');
                    	}                    
                        break;
                    }
                    
                    case 'set_member':
                    {
                        break;
                    }
                    
                    case 'member_login':
                    {
                        $arr_return = $this->_member_login($arr_param);
                        break;
                    }
                    
                    case 'register_member':
                    {
                        $arr_return = $this->_register_member($arr_param);
                        break;
                    }
                    
                    case 'get_match_list':
                    {
                        $arr_return = $this->_get_match_list($arr_param);
                        break;
                    }
                    
                    case 'get_match_integral_shooter':
                    {
                        $arr_return = $this->_get_match_integral_shooter($arr_param);
                        break;
                    }
                    
                    case 'get_league_list':
                    {
                        $arr_return = $this->_get_league_list($arr_param);
                        break;
                    }
                    
                    case 'get_team_list':
                    {
                        $arr_return = $this->_get_team_list($arr_param);
                        break;
                    }
                    
                    case 'get_team_detail':
                    {
                        $arr_return = $this->_get_team_detail($arr_param);
                        break;
                    }
                    case 'get_god_comment':
                    {
                        $arr_return = $this->_get_god_comment($arr_param);
                        break;
                    }
                    case 'get_news_list':
                    {
                        $arr_return = $this->_get_news_list($arr_param);
                        break;
                    }
                    case 'get_news_detail':
                    {
                        $arr_return = $this->_get_news_detail($arr_param);
                        break;
                    }
                    case 'edit_member_info':
                    {
                        $arr_return = $this->_edit_member_info($arr_param);
                        break;
                    }
                    case 'search_member':
                    {
                        $arr_return = $this->_search_member($arr_param);
                        break;
                    }
                    case 'get_lq_match_list':
                    {
                        $arr_return = $this->_get_lq_match_list($arr_param);
                        break;
                    }
                    case 'change_password':
                    {
                        $arr_return = $this->_change_password($arr_param);
                        break;
                    }
                    case 'find_password' :
                    {
                        $arr_return = $this->_find_password($arr_param); // 之前的
                        break;
                    }
					case 'resetpass_email':
					{
						$bln_only_email = TRUE;
						$arr_return = $this->_find_password($arr_param, $bln_only_email);
						break;
					}
					case 'bind_email2':
					{
						$arr_param['not_need_pwd'] = 1;
					}
					case 'bind_email':
                    {

                        $arr_return = $this->_bind_email($arr_param);
                        break;
                    }
                    case 'edit_member_avatar':
                    {
                        $arr_return = $this->_edit_member_avatar($arr_param);
                        break;
                    }
					
					case 'send_phone_verify':
					{
						// 1. 注册
						// 2. 重置密码
						// 3. 验证手机身份 // 这个好像没有什么用？
						// 4. 绑定新手机号
						if($arr_param['type'] == '1')
						{
							$action = 'register_mobile_1';
						}
						elseif($arr_param['type'] == '2')
						{
							$action = 'resetpwd_mobile_1';
						}
						elseif($arr_param['type'] == '4')
						{
							$action = 'bind_mobile_1';
						}
						$arr_return = $this->_action($action, $arr_param);
						break;
					}
					
					case 'verify_phone':
					{
						$_token = trim(@$arr_param['token']);
						$_row = passport_token_to_type($_token);
						if(count($_row) == 1)
						{
							$action = $_row[0]['method'].'_'.$_row[0]['type'].'_2';
							$arr_return = $this->_action($action, $arr_param);
						}
						break;
					}
					
					case 'register_mobile_1':
					{
						// zhangjh 2015-03-18 
						if(trim($arr_param['verify_code'] != ''))
						{
							$action = 'register_mobile_2';
							$arr_return = $this->_action($action, $arr_param); // 先验证verify_code
							
							if($arr_return['state_code'] == '0')
							{
								$action = 'register_mobile_3';
								$arr_return = $this->_action($action, $arr_param);
							}
						}
						else
						{
							$action = 'register_mobile_3';
							$arr_return = $this->_action($action, $arr_param);
						}
						
						break;
					}
					
					case 'register_mobile_2':
					{
						$action = 'register_mobile_4';
						$arr_return = $this->_action($action, $arr_param);
						break;
					}
					
					case 'register_email_1':
					case 'register_email_2':
					{
						$arr_return = $this->_action($action, $arr_param);
						break;
					}
					case 'resetpass_phone':	// 手机重置密码
					{
						$action = 'resetpwd_mobile_3';
						$arr_return = $this->_action($action, $arr_param);
						break;
					}
					case 'bind_phone2':
					{
						$arr_param['not_need_pwd'] = 1;
					}
					case 'bind_phone':	// 绑定新手机
					{
						$arr_return = $this->_bind_phone($arr_param);
						break;
					}
					case 'register_oauth':
					{
						$arr_return = $this->_register_oauth($arr_param);
						break;
					}
					case 'login_oauth':
					{
						$arr_return = $this->_login_oauth($arr_param);
						break;
					}
					case 'get_match_data':
					{
						$arr_return = $this->_get_match_data($arr_param);
						break;
					}
					case 'get_match_result':
					{
						$arr_return = $this->_get_match_result($arr_param);
						break;
					}
					case 'get_news_detail2':
					{
						$arr_return = $this->_get_news_detail2($arr_param);
						break;
					}
                    default :
                    {
                        $arr_return['state_code'] = '3'; 
                        $arr_return['state_desc'] = '参数错误'; 
                        break;
                    }
                }
            }
            else
            {
                $arr_return['state_code'] = '2'; 
                $arr_return['state_desc'] = 'sign验证错误|正确的是:'.$my_sign;
            }
        }
		echo '<pre>';
        print_r($arr_return);
        $str_return = json_encode($arr_return);
        die($str_return);
    }
	
    public function addview($id,$mid)
    {
        $this->load->model('hits_model');
        $this->hits_model->setHits($id,$mid);
    }
    
    /**
     * 接收从phpsso发送的同步登录、登出
     */
    public function phpsso($code_data)
    {
        $this->load->library('api_out');
        $this->load->model('member_model');
        $this->config->load('common_config');
        $this->load->library('cookie');
        
        $cookie_path = $this->config->item('cookie_path');
        $cookie_domain = $this->config->item('cookie_domain');
        
        $code_data = str_replace('____', '%2', $code_data); // ____变成%2
        
        $phpcms_key = $this->api_out->phpcms_key;
        $json_data = decode(urldecode($code_data), 'D', $phpcms_key);
        $arr_ci_info = json_decode($json_data, TRUE);
        
        $my_sign = md5(md5('ci_sso'.$arr_ci_info['action'].$arr_ci_info['time'].$phpcms_key));
        if($my_sign == $arr_ci_info['sign'])    //验证成功
        {
            $arr_member = array('user_id' => @$_SESSION['user_id'] + 0, 
                                'user_name' => trim(@$_SESSION['user_name']),
                                'avatar_url' => trim(@$_SESSION['avatar_url']));  // 头像URL
            
            $userid = $arr_ci_info['userid'] + 0;
            if($arr_ci_info['action'] == 'synlogin')    // 同步登录
            {
                $data = array('userid' => $userid);
                $row = $this->member_model->get($data);
                // print_r($row);
                $arr_member['user_id'] = $row['userid'];
                $arr_member['user_name'] = $row['username'];
                $arr_member['avatar_url'] = '';
                
                $phpssouid = $row['phpssouid'] + 0;
                if($phpssouid == 0) // 2015-01-16 zhangjh 加没有信息的情况，不处理
                {
                    return false;
                }
                
                $this->session->del($arr_member);       // 注销之前的
                $row2 = $this->member_model->getSsoMember($phpssouid);
                
                if(is_object($row2))
                {
                    $ucuserid = $row2->ucuserid + 0;
                    if($ucuserid > 0)
                    {
                        $arr_member['avatar_url'] = $this->config->item('uc_api_url').'/avatar.php?uid='.$ucuserid.'&size=small';
                    }
                }
                
                if($arr_member['avatar_url'] == '')   // 没有uc的头像，就用pc上的
                {
                    $arr_avatar = $this->_ps_getavatar($phpssouid);
                    $arr_member['avatar_url'] = $arr_avatar['30'];
                }
                
                $this->session->set($arr_member); // 赋值SESSION
                
                // 2014-12-13 处理COOKIE 和 SESSION
                $this->cookie->set($arr_ci_info['cookie'], '', $arr_ci_info['cookietime'], $cookie_domain, $cookie_path);
                $this->session->set($arr_ci_info['cookie']); // 赋值SESSION
                
                
                $auth_key = $arr_ci_info['cookie']['auth'];

                //$cms_auth_key = md5($auth_key.$_SESSION['user_agent']);
                //$cms_auth = $check->sys_auth($userid."\t".$password, 'ENCODE', $cms_auth_key);

                //$_SESSION['auth'] = $cms_auth;
                $_arr_new = array('user' => $userid,
                                  'username' => $arr_ci_info['cookie']['_nickname']);
                $this->session->set($_arr_new);
            }
            elseif($arr_ci_info['action'] == 'synlogout')   // 同步登出
            {
                $this->session->del($arr_member); // 注销之前的
                // 2014-12-13 处理COOKIE 和SESSION
                $this->cookie->del($arr_ci_info['cookie'], $cookie_domain, $cookie_path);
                $this->session->del($arr_ci_info['cookie']);
                
                $_arr_new = array('user' => '',
                                  'username' => '');
                $this->session->del($_arr_new);
                
            }
        }
    }
    
	
	private function _get_match_data($arr_param)
	{
		$arr_return = array();
		
		$match_id = $arr_param['match_id'] + 0;
        $this->load->model('event/match_model');
        $arr_result = $this->match_model->getMatchTechnic($match_id);
        
        if(count($arr_result) > 0)
        {
			$arr_technic_type = array('0' => '先开球',
									  '1' => '第一个角球',
									  '2' => '第一张黄牌',
									  '3' => '射门次数',
									  '4' => '射正次数',
									  '5' => '犯规次数',
									  '6' => '角球次数',
									  '7' => '角球次数(加时)',
									  '8' => '任意球次数',
									  '9' => '越位次数',
									  '10' => '乌龙球数',
									  '11' => '黄牌数',
									  '12' => '黄牌数(加时)',
									  '13' => '红牌数',
									  '14' => '控球时间',
									  '15' => '头球',
									  '16' => '救球',
									  '17' => '守门员出击',
									  '18' => '丢球',
									  '19' => '成功抢断',
									  '20' => '阻截',
									  '21' => '长传',
									  '22' => '短传',
									  '23' => '助攻',
									  '24' => '成功传中',
									  '25' => '第一个换人',
									  '26' => '最后换人',
									  '27' => '第一个越位',
									  '28' => '最后越位',
									  '29' => '换人数',
									  '30' => '最后角球',
									  '31' => '最后黄牌',
									  '32' => '换人数(加时)',
									  '33' => '越位次数(加时)',
									  '34' => '红牌数(加时)',
									  '99' => '比分'	// 非官方类型
									  );
			$arr_rq_type = array('1' => '入球',
								 '2' => '红牌',
								 '3' => '黄牌',
								 '7' => '点球',
								 '8' => '乌龙',
								 '9' => '两黄变红',
								 '98' => '开场',	// 非官方类型
								 '99' => '终场'	// 非官方类型
								 );
			$arr_status = array('-1' => '已结束', '0' => '未开始', '1' => '直播中'); // 现在的
			
			$arr_temp_type = array('1' => '主队', '0' => '客队');
			
			$technic = trim($arr_result[0]['technic']);
			$arr_rq = unserialize($arr_result[0]['rq']);
			$status = trim($arr_result[0]['status']);
			$a_score = trim($arr_result[0]['a_score']);
			$b_score = trim($arr_result[0]['b_score']);
			
			$arr_technic = explode(';', $technic);
			$match_technic = $match_event = array();
			
			$have_start = FALSE;
			foreach($arr_technic as $key => $value)
			{
				// 3,15,4 表示射门次数，主队15次，客队4次
				$_tmp = explode(',', $value);
				$match_technic[] = array('item_code' => $_tmp[0],
										 'item_name' => $arr_technic_type[$_tmp[0]],
										 'host_data' => $_tmp[1],
										 'guest_data' => $_tmp[2]
										 );
				$have_start = TRUE;
			}
			
			// 加个开场事件
			
			if($status == '-1' || $status == '1')
			{
				$match_event[] = array('item_code' => '98',
									   'item_name' => $arr_rq_type['98'],
									   'team_tag' => '',
									   'team_tag_code' => '',
									   'event_time' => '0',
									   'player_id' => '',
									   'player_name_hk' => '',
									   'player_name_cn' => '',
									   );
			}
			
			
			// $match_event = array();
			
			$score0 = $b_score;	// 客队进球数
			$score1 = $a_score;	// 主队进球数
			$last_event_time = '90';
			foreach($arr_rq as $key => $value)
			{
				// 987334^1^3^14^祖爾巴頓^1608^J.巴顿 
				// 表示赛程ID，主客队标志1主0客事件，事件类型 arr_rq ，时间如25，球员名字(可空)，球员(可空)，简体球员名字(可空)
				$_tmp = explode('^', $value);
				$match_event[] = array('item_code' => $_tmp[2],
									   'item_name' => $arr_rq_type[$_tmp[2]],
									   'team_tag' => $arr_temp_type[$_tmp[1]],
									   'team_tag_code' => $_tmp[1],
									   'event_time' => $_tmp[3],
									   'player_id' => $_tmp[5],
									   'player_name_hk' => $_tmp[4],
									   'player_name_cn' => $_tmp[6],
									   );
				if($_tmp[1] >= $last_event_time)
				{
					$last_event_time = $_tmp[1];
				}
				/*
				if(($_tmp[2] == 1 && $_tmp[1] == 1) || ($_tmp[2] == 8 && $_tmp[1] == 0) || ($_tmp[2] == 7 && $_tmp[1] == 1))
				{
					$score1++;
				}
				if(($_tmp[2] == 1 && $_tmp[1] == 0) || ($_tmp[2] == 8 && $_tmp[1] == 1) || ($_tmp[2] == 7 && $_tmp[1] == 0))
				{
					$score0++;
				}
				*/
			}
			
			// 加个终场事件
			if($status == '-1')
			{
				$match_event[] = array('item_code' => '99',
									   'item_name' => $arr_rq_type['99'],
									   'team_tag' => '',
									   'team_tag_code' => '',
									   'event_time' => $last_event_time,
									   'player_id' => '',
									   'player_name_hk' => '',
									   'player_name_cn' => '',
									   );
			}
			
			// 加个比分
			$str_score = $score1.":".$score0;
			$match_technic[] = array('item_code' => '99',
									 'item_name' => $arr_technic_type['99'],
									 'host_data' => trim($score1),
									 'guest_data' => trim($score0)
									 );
            $arr_return['state_code'] = '0'; 
            $arr_return['state_desc'] = '成功';
            $arr_return['match_technic'] = $match_technic;
			$arr_return['match_event'] = $match_event;
			
			
        }
        else
        {
            $arr_return['state_code'] = '1'; 
            $arr_return['state_desc'] = '没有信息';
        }
        //echo "<pre>";
		//print_r($arr_return);
        return $arr_return;
	}
	
    /**
     * 根据phpsso uid获取头像url
     * @param int $uid 用户id
     * @return array 四个尺寸用户头像数组
     */
    public function _ps_getavatar($uid) {
        $dir1 = ceil($uid / 10000);
        $dir2 = ceil($uid % 10000 / 1000);
        $url = $this->config->item('ps_api_url').'/uploadfile/avatar/'.$dir1.'/'.$dir2.'/'.$uid.'/';
        $avatar = array('180'=>$url.'180x180.jpg', '90'=>$url.'90x90.jpg', '45'=>$url.'45x45.jpg', '30'=>$url.'30x30.jpg');
    }
    
    /**
     * 根据用户ID数组(json格式)取用户信息
     * @param string $str_userid 用户ID数组
     * @return array
     */
    private function _get_member($arr_param)
    {
        $arr_return = array();
        
        $this->load->model('member_model');
        
        //$arr_userid = json_decode(urldecode($arr_param['userid']), TRUE);
        $arr_userid = explode(',', $arr_param['userid']);
        if(!is_array($arr_userid))
        {
            $arr_return['state_code'] = '4'; 
            $arr_return['state_desc'] = '用户格式错误';
            return $arr_return;
        }
        
       // $i = 0;
        $arr_detail = array();
        foreach($arr_userid as $key => $value)
        {
            $userid = $value;
            $user_info = $this->member_model->getMember($userid);
           
            if(is_object($user_info))
            {
                
                $modelid = $user_info->modelid;
                $this->load->model('model_model');              
                $model = $this->model_model->get($modelid);
                $tablename = @$model->tablename;
				$user_detail = $this->member_model->getMemberDetail($user_info->userid);
                if($tablename == 'member_detail' && is_object($user_detail))   // todo 只处理一个 member_detail 的用户
                {
                		$this->load->model('linkage_model');
                        $sex = $user_detail->gender == '' ? '0' : $user_detail->gender;
                        $address_text = trim($user_detail->address);						
						$address = $address_text==''?$this->linkage_model->getAddress($user_detail->city):$address_text;                     
                        $signiture = $user_detail->introduction;
						$mobile = $user_detail->mobile_no;
						$bind_email = $user_detail->bind_email;
						$bind_mobile = $user_detail->bind_mobile;
						$album = passport_get_discuz_album($user_info->userid);
						$birthday = $user_detail->birthday;
						$education = $user_detail->education;
						$income = $user_detail->income;
                }
                else
                {
                    $sex = '0';
                    $address = '';
                    $signiture = '';
					$mobile = '';
					$bind_email = '';
					$bind_mobile = '';
					$album = '';
					$birthday = '';
					$education = '';
					$income = '';
                }
                
                $logo_url = getmemberucavatar($user_info->phpssouid);
                $logo_url = (is_array($logo_url)) ? '' : $logo_url;
                
                //$passport_avatar_exist = passport_avatar_exist($user_info->userid, 'middle');
                //if($passport_avatar_exist === TRUE)
                //{
                    $logo_url = passport_avatar_show($user_info->userid, 'middle', TRUE);
                //}
				
				$arr_detail[$key] = array( 'userid' => $user_info->userid,
                                         'username' => $user_info->username,
                                         'logo' => $logo_url,
                                         'email' => $user_info->email,
                                         'nickname' => $user_info->nickname,
                                         'sex' => $sex,
                                         'address' => $address,
                                         'signiture' => $signiture,
										 'mobile_phone' => $mobile,
										 'bind_email' => $bind_email,
										 'bind_mobile' => $bind_mobile,
										 'album' => $album,
										 'birthday' => $birthday,
										 'education' => $education,
										 'income' => $income
										 );
               // $i++;
            }
        }
        
        if(count($arr_detail) > 0)
        {
            $arr_return['state_code'] = '0'; 
            $arr_return['state_desc'] = '成功';
            $arr_return['user_detail'] = $arr_detail;
        }
        else
        {
            $arr_return['state_code'] = '1'; 
            $arr_return['state_desc'] = '没有信息';
        }
        
        return $arr_return;
    }
    
    private function _register_member($arr_param)
    {
        $arr_return = array();
        $arr_input = array();
        
        $arr_input = array(	'username' => trim(@$arr_param['username']),		// 账号
							'password' => trim(@$arr_param['password']),		// 密码
							'pwdconfirm' => trim(@$arr_param['password']),	// 确认密码
							'email' => trim(@$arr_param['email']),			// email
							'nickname' => (trim(@$arr_param['nickname']) == '' ? trim(@$arr_param['username']) : trim(@$arr_param['nickname'])),    // 昵称
							'mobile' => trim(@$arr_param['mobile']),		// 手机号码
							'sms_verify' => '',								// 手机验证码
							'passport' => 0,								// 是否从用户中心注册
							'from' => trim(@$arr_param['from']) == '' ? 'api' : trim(@$arr_param['from']),	// 从什么地方注册
							'method' => @$arr_param['method'] == '' ? 'email' : trim(@$arr_param['method']),	// 注册方法 email or mobile
							'connectid' => trim(@$arr_param['connectid']),			// 第三方用
							'remote_avatar' => trim(@$arr_param['remote_avatar']),	// 第三方用
							'verify_code' => '',									// 验证码
							'gender' => trim(@$arr_param['sex']) == '' ? '0' : trim(@$arr_param['sex']),	// 性别
						);
        
        $return = passport_register($arr_input);
        
        if($return['success'] === TRUE)
        {
            $logo_url = getmemberucavatar($return['uid'] + 0);
            //$passport_avatar_exist = passport_avatar_exist($return['userid'], 'middle');
            //if($passport_avatar_exist === TRUE)
            //{
                $logo_url = passport_avatar_show($return['userid'], 'middle', TRUE);
            //}
            
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['member_logo'] = $logo_url;
            $arr_return['userid'] = $return['userid'];
            $arr_return['nick_name'] = $return['nickname'];
            $arr_return['username'] = $arr_input['username'];
            $arr_return['email'] = $arr_input['email'];

			$arr_return['mobile_phone'] = $arr_input['mobile'];
			$arr_return['bind_email'] = '0';
			$arr_return['bind_mobile'] = $arr_input['method'] == 'mobile' ? '1' : '0';
        }
        else
        {
            $arr_return['state_code'] = '5';
            $arr_return['state_desc'] = '注册失败|'.$return['message'].$return['uc_msg'];
            
            if($return['message'] == '用户名已经存在')
            {
                $arr_return['state_code'] = '9';
                $arr_return['state_desc'] = '用户名已经存在';
                
                // 查用户信息
                $this->load->model('member_model');
                $this->load->model('linkage_model');
                $data = array('username' => $arr_input['username']);
                $row = $this->member_model->get($data);
                
                $logo_url = getmemberucavatar($row['phpssouid'] + 0);
                //$passport_avatar_exist = passport_avatar_exist($row['userid'], 'middle');
                //if($passport_avatar_exist === TRUE)
                //{
                    $logo_url = passport_avatar_show($row['userid'], 'middle', TRUE);
                //}
            
                $arr_return['member_logo'] = $logo_url;
                $arr_return['userid'] = $row['userid'];
                $arr_return['username'] = $arr_input['username'];
                $arr_return['nick_name'] = trim(@$row['nickname']);
                $arr_return['email'] = $arr_input['email'];
                
                $user_detail = $this->member_model->getMemberDetail($row['userid']);
                if(is_object($user_detail))
                {
                    $arr_return['sex'] = $user_detail->gender == '' ? '0' : $user_detail->gender;
                    
                    $address_text = trim($user_detail->address);
                    if($address_text == '')
                    {
                        $arr_return['address'] = $this->linkage_model->getAddress($user_detail->city);
                    }
                    else
                    {
                        $arr_return['address'] = $address_text;
                    }
                        
                    $arr_return['signiture'] = $user_detail->introduction;
					$arr_return['mobile_phone'] = $user_detail->mobile_no;
					$arr_return['bind_email'] = $user_detail->bind_email;
					$arr_return['bind_mobile'] = $user_detail->bind_mobile;
					$arr_return['album'] = passport_get_discuz_album($row['userid']);
					$arr_return['birthday'] = $user_detail->birthday;
					$arr_return['education'] = $user_detail->education;
					$arr_return['income'] = $user_detail->income;
                }
                else
                {
                    $arr_return['sex'] = '0';
                    $arr_return['address'] = '';
                    $arr_return['signiture'] = '';
					$arr_return['mobile_phone'] = '';
					$arr_return['bind_email'] = '';
					$arr_return['bind_mobile'] = '';
					$arr_return['album'] = '';
					$arr_return['birthday'] = '';
					$arr_return['education'] = '';
					$arr_return['income'] = '';
                }
            }
        }
        return $arr_return;
    }
    
	// 后来写的分步的Email注册、手机注册和第三方注册
	private function _action($action, $arr_param)
	{
		$arr_return = $arr_input = array();
		
		$_tmp = explode('_', $action);
		if(count($_tmp) != 3)
		{
			$arr_return['state_code'] = '4';
			$arr_return['state_desc'] = '参数不足|action参数不足';
		}
		else
		{
			if($_SERVER['REMOTE_ADDR'] == '192.168.2.93') // 本机就输出
			{
				// echo "<pre>";
			}
			
			$data = array();
			$data['method'] = $_tmp[0];		// 注册
			$data['type'] = trim($_tmp[1]);	// email/mobile
			$data['step'] = trim($_tmp[2]);	// 第几步 1/2/3...
			$data['from'] = trim(@$arr_param['from']) == '' ? 'api' : trim(@$arr_param['from']);	// api qq sina weixin
			$data['email'] = trim(@$arr_param['email']);
			$data['password'] = trim(@$arr_param['password']); // 记录明码，同步注册UC用
			$data['log_time'] = time();
			$data['is_deleted'] = '0';
			$data['connectid'] = trim(@$arr_param['connectid']);
			$data['remote_avatar'] = trim(@$arr_param['remote_avatar']);
			$data['username'] = trim(@$arr_param['email']);
			$data['nickname'] = trim(is_gbk(@$arr_param['nickname']) ? arr_to_utf8(@$arr_param['nickname']) : @$arr_param['nickname']);
			$data['mobile'] = trim(@$arr_param['phone']);
			$data['sms_verify'] = trim(@$arr_param['verify_code']);
			$data['token'] = passport_make_token($data);
			
			// 邮箱注册
			if($data['method'] == 'register' && $data['type'] == 'email')
			{
				if($data['step'] == '1')	// 第一步
				{
					if(!is_email($data['email']))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的Email';
					}
					elseif(!is_password(trim($arr_param['password'])))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的密码';
					}
					else
					{
						$pass = passport_email_can_register($data['email']);	// 是否可以注册
						if($pass['success'] === FALSE)
						{
							$arr_return['state_code'] = '3';
							$arr_return['state_desc'] = '参数错误|'.$pass['message'];
						}
						else
						{
							$return = passport_token($data);
							if($return['success'] === TRUE)
							{
								$arr_return['state_code'] = '0';
								$arr_return['state_desc'] = '成功';
								$arr_return['token'] = $return['message'];
							}
							else
							{
								$arr_return['state_code'] = '8';
								$arr_return['state_desc'] = '操作失败';
								$arr_return['token'] = $return['message'];
							}
						}
					}
				}
				elseif($data['step'] == '2')	// 第二步
				{
					if(!is_username(trim($data['nickname'])))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的昵称';
					}
					else
					{
						$data['token'] = trim(@$arr_param['token']);
						$return = passport_token($data);
						if($return['success'] === TRUE) // 可以注册
						{
							// 组织要注册的参数
							$arr_param = array();
							$arr_param['username'] = $return['userinfo'][0]['username'];
							$arr_param['password'] = $return['userinfo'][0]['password'];
							$arr_param['pwdconfirm'] = '';
							$arr_param['email'] = $return['userinfo'][0]['email'];
							$arr_param['nickname'] = $return['userinfo'][0]['nickname'];
							$arr_param['mobile'] = $return['userinfo'][0]['mobile'];
							$arr_param['sms_verify'] = '';
							$arr_param['passport'] = '0';
							$arr_param['from'] = $return['userinfo'][0]['from'];
							$arr_param['method'] = $return['userinfo'][0]['type'];
							$arr_param['connectid'] = $return['userinfo'][0]['connectid'];
							$arr_param['remote_avatar'] = $return['userinfo'][0]['remote_avatar'];
							//$arr_param['verify_code'] = '';
							$arr_param['gender'] = '0';
							$arr_return = $this->_register_member($arr_param);
						}
						else
						{
							$arr_return['state_code'] = '8';
							$arr_return['state_desc'] = $return['message'];
						}
					}
				}
			}
			// 手机注册|手机号码找回密码 resetpwd_mobile_1
			elseif(($data['method'] == 'register' || $data['method'] == 'resetpwd' || $data['method'] == 'bind') && $data['type'] == 'mobile')
			{
				if($data['step'] == '1') //第一步|发送验证码
				{
					if(!is_mobile($data['mobile']))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的手机号码';
					}
					else
					{
						$pass = passport_mobile_can_register($data['mobile']);
						$data['timestamp'] = trim(@$arr_param['timestamp']) + 0;
						if($data['method'] == 'resetpwd') // 找回密码要注册过的手机号码
						{
							$data['newpwd'] = trim(@$arr_param['newpwd']);
							if($pass['success'] === FALSE)
							{
								$return = passport_token($data);
								if($return['success'] === TRUE)
								{
									$arr_return['state_code'] = '0';
									$arr_return['state_desc'] = '发送验证码成功';
									$arr_return['token'] = $return['message'];
								}
								else
								{
									$arr_return['state_code'] = '8';
									$arr_return['state_desc'] = '操作失败';
								}
							}
							else
							{
								$arr_return['state_code'] = '3';
								$arr_return['state_desc'] = '参数错误|手机号码没有注册过';
							}
						}
						else
						{
							// 注册手机和绑定新手机用
							$pass = passport_mobile_can_register($data['mobile']);
							if($pass['success'] === TRUE)
							{
								$return = passport_token($data);
								if($return['success'] === TRUE)
								{
									$arr_return['state_code'] = '0';
									$arr_return['state_desc'] = '发送验证码成功';
									$arr_return['token'] = $return['message'];
								}
								else
								{
									$arr_return['state_code'] = '8';
									$arr_return['state_desc'] = '操作失败';
								}
							}
							else
							{
								$arr_return['state_code'] = '3';
								$arr_return['state_desc'] = '参数错误|手机号码已经注册过';
							}
						}
					}
				}
				elseif($data['step'] == '2') //第二步|校验验证码
				{
					$data['token'] = trim(@$arr_param['token']);
					$return = passport_token($data);
					if($return['success'] === TRUE)
					{
						$arr_return['state_code'] = '0';
						$arr_return['state_desc'] = '校验通过';
						if($data['method'] == 'bind')
						{
							$arr_return['userinfo'] = $return['userinfo'];
						}
					}
					else
					{
						$arr_return['state_code'] = '8';
						$arr_return['state_desc'] = $return['message'];
					}
				}
				elseif($data['step'] == '3') //第三步|输入密码
				{
					if(!is_password(trim(@$arr_param['newpwd'])) && !is_password(trim(@$arr_param['password'])))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的密码';
					}
					else
					{
						$data['token'] = trim(@$arr_param['token']);
						$return = passport_token($data);
						if($return['success'] === TRUE)
						{
							if($data['method'] == 'resetpwd')	// 如果是手机找回密码，要修改密码
							{
								// 组织要修改的参数
								$arr_param = array();
								$arr_param['userid'] = $return['userinfo'][0]['userid'];
								$arr_param['from'] = $return['userinfo'][0]['from'];
								$arr_param['new_pwd'] = $return['userinfo'][0]['password'];
								$arr_param['reset_password'] = '1';
								$return2 = passport_change_password($arr_param);
								
								if($return2['success'] === TRUE)
								{
									$arr_return['state_code'] = '0';
									$arr_return['state_desc'] = '重置密码成功';
								}
								else
								{
									$arr_return['state_code'] = '8';
									$arr_return['state_desc'] = $return2['message'];
								}
							}
							else
							{
								$arr_return['state_code'] = '0';
								$arr_return['state_desc'] = '成功';
								$arr_return['token'] = $data['token'];
							}
						}
						else
						{
							$arr_return['state_code'] = '8';
							$arr_return['state_desc'] = $return['message'];
						}
					}
				}
				elseif($data['step'] == '4')//第四步|输入昵称并注册
				{
					if(!is_username(trim($data['nickname'])))
					{
						$arr_return['state_code'] = '3';
						$arr_return['state_desc'] = '参数错误|不是一个有效的昵称';
					}
					else
					{
						$data['token'] = trim(@$arr_param['token']);
						$return = passport_token($data);
						if($return['success'] === TRUE) // 可以注册
						{
							// 组织要注册的参数
							$arr_param = array();
							$arr_param['username'] = $return['userinfo'][0]['username'];
							$arr_param['password'] = $return['userinfo'][0]['password'];
							$arr_param['pwdconfirm'] = '';
							$arr_param['email'] = $return['userinfo'][0]['email'];
							$arr_param['nickname'] = $return['userinfo'][0]['nickname'];
							$arr_param['mobile'] = $return['userinfo'][0]['mobile'];
							$arr_param['sms_verify'] = '';
							$arr_param['passport'] = '0';
							$arr_param['from'] = $return['userinfo'][0]['from'];
							$arr_param['method'] = $return['userinfo'][0]['type'];
							$arr_param['connectid'] = $return['userinfo'][0]['connectid'];
							$arr_param['remote_avatar'] = $return['userinfo'][0]['remote_avatar'];
							//$arr_param['verify_code'] = '';
							$arr_param['gender'] = '0';
							$arr_return = $this->_register_member($arr_param);
						}
						else
						{
							$arr_return['state_code'] = '8';
							$arr_return['state_desc'] = $return['message'];
						}
					}
				}
			}
			else
			{
				
			}
		}
		return $arr_return;
	}
	
	private function _time2GameTime($state,$start_time)
	{
		$goTime = '0';
		switch($state){
			case 1:
				if($start_time!=0){
					$second = (time() - $start_time ) / 60;
					$goTime = floor($second);
					if($goTime<1){
						$goTime='1';
					}
					if($goTime>45){
						$goTime='45+';
					}
				}
				break;
			case 2:
				if($start_time!=0){
					//$second = (time() - $start_time ) / 60;
					$goTime = '中';
		}
				break;
			case 3:
				if($start_time!=0){
					$second = (time() - $start_time ) / 60;
					$goTime = 46 + floor($second);
					if($goTime<46){
						$goTime='46';
					}
					if($goTime>90){
						$goTime='90+';
					}
				}
				break;
		}
		return $goTime.'';
	}
	
	private function _get_match_result($arr_param)
	{
		$arr_return = array();
		$arr_match = explode(',', trim($arr_param['match_id']));
		$match_result = array();
		
		if(is_array($arr_match))
		{
			$this->load->model('event/match_model');
			foreach($arr_match as $key => $value)
			{
				$row = $this->match_model->getMatch($value + 0);
				if(is_object($row))
				{
					$_tmp = array();
					$_tmp['game_id'] = $row->id;
					$_tmp['team1_score'] = $row->a_score;
					$_tmp['team2_score'] = $row->b_score;
					$_tmp['game_state'] = $row->status;
					$_tmp['game_desc'] = $this->match_model->statuslist[$row->status];
					$_tmp['live_starttime'] = date('Y-m-d H:i:s', $row->match_time);
					$_tmp['live_endtime'] = date('Y-m-d H:i:s', $row->end_time);
					
					
					// 取basisMatchOdds表的时间
					$row2 = $this->match_model->getMatchOdds($row->band_id + 0);
					if(is_object($row2))
					{
						$_tmp['game_time'] = $this->_time2GameTime($row->status, $row2->start_time);
					}
					else
					{
						$_tmp['game_time'] = '';
					}

					$match_result[] = $_tmp;
				}
			}
		}
		if(count($match_result > 0))
		{
			$arr_return['state_code'] = '0';
			$arr_return['state_desc'] = '操作成功';
			$arr_return['match_result'] = $match_result;
		}
		else
		{
			$arr_return['state_code'] = '1';
			$arr_return['state_desc'] = '没有信息';
		}
		
		
		return $arr_return;
	}
	
    private function _get_match_list($arr_param)
    {
        $arr_return = array();
        $arr_input = array();
        
        //echo "<pre>";
        //print_r($arr_param);
        $client_type = $arr_param['client_type'] + 0;   // 客户端类型 0:Android 1:IOS
		
		// 联赛ID和球队ID，要么就不用传，要么就只能传一个
		$league_id = trim(@$arr_param['league_id']);	// 联赛ID 如果不传此参数，则表示不指定联赛
		$team_id = trim(@$arr_param['team_id']);		// 球队ID 如果不传此参数，则表示不指定球队
		$match_id = trim(@$arr_param['match_id']);		// 可以根据赛程ID's，批量取信息
		
		$top_id = trim(@$arr_param['top_id']);          // 列表上拉时会传这个值，传的是某天的日期，查出这个日期之前的N组数据，N的值由下面的page_size决定 top_id和last_id要么就不用传，要么就只能传一个
        $last_id = trim(@$arr_param['last_id']);        // 列表下拉时会传这个值，传的是某天的日期，查出这个日期之后的N组数据，N的值由下面的page_size决定
        $page_size = $arr_param['page_size'] + 0;       // 请求多少组 当top_id和last_id都不传时，默认获得14组的比赛数据，即本周和下周的比赛 当top_id和last_id有传时，组的数量由page_size决定
        $is_liaoqiu = @$arr_param['is_liaoqiu'] + 0;    // 是否取聊球
		
        
        if($last_id != '' && $top_id != '')
        {
            $arr_return['state_code'] = '3';
            $arr_return['state_desc'] = '参数错误|top_id和last_id只需要一个';
            return $arr_return;
        }
        
        if($top_id != '')
        {
            if(is_date($top_id))
            {
                $end_time = strtotime($top_id." 23:59:59") - 1 * (24 * 60 * 60);
                $start_time = $end_time - ($page_size * 24 * 60 * 60);
            }
            else
            {
                $arr_return['state_code'] = '3';
                $arr_return['state_desc'] = '参数错误|top_id';
                return $arr_return;
            }
        }
        
        if($last_id != '')
        {
            if(is_date($last_id))
            {
                $start_time = strtotime($last_id." 00:00:00") + 1 * (24 * 60 * 60);
                $end_time = $start_time + ($page_size * 24 * 60 * 60);
            }
            else
            {
                $arr_return['state_code'] = '3';
                $arr_return['state_desc'] = '参数错误|last_id';
                return $arr_return;
            }
        }
        
        if($last_id == '' && $top_id == '')
        {
            $start_time = strtotime(date('Y-m-d')." 00:00:00");
			// $start_time = strtotime(date('Y-m-d', time()-86400*date('w'))." 00:00:00");	// 一周开始是周日
			// 不查过去的比赛
            
			$end_time = $start_time + (14 * 24 * 60 * 60) - 1;
			// 请求多少组 当top_id和last_id都不传时，默认获得14组的比赛数据，即本周和
        }

        $where  = 'match_time >= '.$start_time.' ';
        $where .= 'AND match_time <= '.$end_time.' ';
		$where2 = '1 = 1 ';	// 如果没有数据，不使用时间查询条件(全体足球比赛)
		$where3 = '1 = 1 ';	// (全体比赛)
		
		if($league_id != '' && strtolower($league_id) != 'null')
		{
			$where .= 'AND league_id in ('.$league_id.') ';
			$where2 .= 'AND league_id in ('.$league_id.') ';
		}
		else
		{
			//$where .= 'AND league_id in (1, 15, 13, 20) ' ; // 7=>'NBA', 8=>'CBA', '1=>'英超', 15=>'西甲', 13=>'中超', 20=>'欧冠'
			//$where2 .= 'AND league_id in (1, 15, 13, 20) ';
			$where .= 'AND event_id = 1 '; // 1表示足球
			$where2 .= 'AND event_id = 1 '; // 1表示足球
		}
		
		if($team_id != '' && strtolower($team_id) != 'null')
		{
			$where .= 'AND (a_id in ('.$team_id.') OR b_id in ('.$team_id.')) ';
			$where2 .= 'AND (a_id in ('.$team_id.') OR b_id in ('.$team_id.')) ';
		}
		
		if($match_id != '' && strtolower($match_id) != 'null')
		{
			$where .= 'AND id in ('.$match_id.') ';
			$where2 .= 'AND id in ('.$match_id.') ';
		}
       
        $where .= 'AND display = 1 ';
        if($is_liaoqiu == 1)
        {
            $where .= 'AND display_liaogeqiu = 1 ';
			$where2 .= 'AND display_liaogeqiu = 1 ';
			$where3 .= 'AND display_liaogeqiu = 1 ';
        }
        
        $this->load->model('event/match_model');
        $row = $this->match_model->getMatchList('*', 'match', $where, 0, 1000, 'match_time DESC'); // 取最后的
		//echo "<pre>";
		//print_r($row);
        
        $arr_game = array();
		
		if(count($row) == 0 && (($last_id == '' && $top_id == '') || $top_id == ''))	// 如果没有数据，2015-04-09修改逻辑
		{
			$page_size = 1;
			$row = $this->match_model->getMatchList('*', 'match', $where2, 0, 1000, 'match_time DESC');
		}
		//print_r($row);
		
		if(count($row) == 0 && ($last_id == '' && $top_id == ''))	// 还是没有数据的话
		{
			$page_size = 1;
			$row = $this->match_model->getMatchList('*', 'match', $where3, 0, 1000, 'match_time DESC');
		}
		
		/*
		for($i = count($row) - 1; $i >= 0; $i--)
		{
			if(count($arr_game) >= $page_size)	// 取14组
				break;
			$date = date('Y-m-d', $row[$i]->match_time);
			$arr_game[$date][] = $row[$i];
		}
		*/
		// 这里才取倒序
		$page_size = $page_size == 0 ? 14 : $page_size;
		$_arr_game = $arr_game = $_arr_game2 = array();
		
		for($i = 0; $i < count($row); $i++)
		{
			$date = date('Y-m-d', $row[$i]->match_time);
			$_arr_game[$date][] = $row[$i];
			if(count($_arr_game) > $page_size)	// 取14组
			{
				unset($_arr_game[$date]);
				break;
			}
		}
		
		foreach($_arr_game as $key => $value)
		{
			$_arr_game2[][$key] = $_arr_game[$key];
		}
		
		for($i = count($_arr_game2) - 1; $i >= 0; $i--)
		{
			foreach($_arr_game2[$i] as $key => $value)
			{
				$arr_game[$key] = $value;
			}
		}

        $game_array = array();

        foreach($arr_game as $key => $value)
        {
            $_arr = array();
            
            $date = strtotime($key);
            $week_cn = array(0 => '周日', 1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六');
            $_arr['date'] = date('Y-m-d', $date);
			$_arr['week'] = $week_cn[date('w', $date)];
            
            foreach($value as $key2 => $value2)
            {
                $arr_status = array('-1' => '已结束', '0' => '未开始', '1' => '直播中'); // 现在的
                $arr_live = unserialize($value2->live);
                $live_url = trim(@$arr_live[0]['url']); //$live_url 取数组的第一个吧
				
				// 查询第几轮
				if($value2->band_id > 0)
				{
					$round_return = $this->match_model->getMatchRound($value2->band_id);
					if(is_numeric($round_return))
					{
						$round_return = $value2->league_name.'-第'.$round_return.'轮';
					}
				}
				else
				{
					$round_return = '';
				}
                $_arr['games'][] = array('game_id' => $value2->id,
                                         'image1_url' => $value2->a_logo,
                                         'image2_url' => $value2->b_logo,
                                         'team1_name' => $value2->a_name,
                                         'team2_name' => $value2->b_name,
                                         'round' => $round_return,
                                         'team1_score' => $value2->a_score,
                                         'team2_score' => $value2->b_score,
                                         'game_state' => $value2->status,   // '-1' => '已结束', '0' => '未开始', '1' => '直播中'
                                         'game_desc' => $arr_status[$value2->status],
                                         'live_url' => $live_url,
                                         'is_live' => '',           // 这个要加字段 0：点播 1：直播
                                         'live_starttime' => '',    // 这个要加字段 直播开始时间
                                         'live_endtime' => '',      // 这个要加字段 直播结束时间
                                         'live_pre_url' => '',      // 这个要加字段
                                         'live_record_url' => '',   // 这个要加字段
                                         'live_state' => '',        // 这个要加字段 0：未开始 1：直播中 2：已结束 3：已取消
                                         'live_desc' => '',         // 这个要加字段
                                         'game_time' => date('Y-m-d H:i:s', $value2->match_time),
                                         'commont_count' => '',     // 这个要加字段
                                         'team1_id' => $value2->a_id,
                                         'team2_id' => $value2->b_id,
                                         'display_liaogeqiu' => $value2->display_liaogeqiu
                                         );
            }
            
            $game_array[] = $_arr;
        }
        
        $arr_return['state_code'] = '0';
        $arr_return['state_desc'] = '成功';
        
        $arr_return['game_array'] = $game_array;
		//print_r($arr_return);
        return $arr_return;
    }
    
    private function _get_lq_match_list($arr_param)
    {
        $arr_param['is_liaoqiu'] = 1;
        return $this->_get_match_list($arr_param);
    }
    
    private function _member_login($arr_param)
    {
        $arr_return = array();
        $this->load->model('member_model');
        $this->load->model('linkage_model');
        
        $username = trim($arr_param['username']);
        $password = trim($arr_param['password']);
        
        $data = array('username' => $username);
        $row = $this->member_model->get($data);
		
		// m3
		if(count($row) == 0)
		{
			if(is_mobile($username))
			{
				$data = array('mobile' => $username);
				$row = $this->member_model->getMemberByMobile($data);
			}
			elseif(is_email($username))
			{
				$data = array('email' => $username);
				$row = $this->member_model->get($data);
			}
		}
		
        if(count($row) > 0)
        {
            $md5_password = md5(md5($password).$row['encrypt']);
            if($row['password'] == $md5_password)   // phpcms验证成功
            {
                // 登录成功更新用户最近登录时间和ip
                $arr_data = array(
                    'lastdate' => time(),
                    'lastip' => ip(),
                    'loginnum' => $row['loginnum'] + 1
                );
                $this->member_model->update($arr_data, $row['userid']);
                
                $logo_url = getmemberucavatar($row['phpssouid'] + 0);
                //$passport_avatar_exist = passport_avatar_exist($row['userid'], 'middle');
                //if($passport_avatar_exist === TRUE)
                //{
                    $logo_url = passport_avatar_show($row['userid'], 'middle', TRUE);
                //}
                
                $arr_return['state_code'] = '0';
                $arr_return['state_desc'] = '成功';
                $arr_return['username'] = $row['username'];
                $arr_return['userid'] = $row['userid'];
                $arr_return['member_logo'] = $logo_url;
                $arr_return['nick_name'] = trim(@$row['nickname']);
                $arr_return['email'] = $row['email'];
                
                $sex = trim(@$arr_param['sex']) + 0;
                $address = trim(@$arr_param['address']);
                $signiture = $row['userid'];
                
                $user_detail = $this->member_model->getMemberDetail($row['userid']);
                if(is_object($user_detail))
                {
                    $arr_return['sex'] = $user_detail->gender == '' ? '0' : $user_detail->gender;
                    
                    $address_text = trim($user_detail->address);
                    if($address_text == '')
                    {
                        $arr_return['address'] = $this->linkage_model->getAddress($user_detail->city);
                    }
                    else
                    {
                        $arr_return['address'] = $address_text;
                    }
                    
                    $arr_return['signiture'] = $user_detail->introduction;
					$arr_return['mobile_phone'] = $user_detail->mobile_no;
					$arr_return['bind_email'] = $user_detail->bind_email;
					$arr_return['bind_mobile'] = $user_detail->bind_mobile;
					$arr_return['album'] = passport_get_discuz_album($row['userid']);
					$arr_return['birthday'] = $user_detail->birthday;
					$arr_return['education'] = $user_detail->education;
					$arr_return['income'] = $user_detail->income;
                }
                else
                {
                    $arr_return['sex'] = '0';
                    $arr_return['address'] = '';
                    $arr_return['signiture'] = '';
					$arr_return['mobile_phone'] = '';
                    $arr_return['bind_email'] = '';
                    $arr_return['bind_mobile'] = '';
					$arr_return['album'] = '';
					$arr_return['birthday'] = '';
					$arr_return['education'] = '';
					$arr_return['income'] = '';
                }
                
                passport_write_log($row['userid'], '用户登录|api', '登录成功');
            }
            else
            {
                $arr_return['state_code'] = '6';
                $arr_return['state_desc'] = '账号或密码错误';
                passport_write_log($row['userid'], '用户登录|api', '登录失败|账号或密码错误');
            }
        }
        else
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
            passport_write_log(0, '用户登录|api', '登录失败|没有信息');
        }
        return $arr_return;
    }
    
    private function _get_match_integral_shooter($arr_param)
    {
        $arr_return = array();
        $this->load->model('event/match_model');
        $data = array();
        $data['league_id'] = trim($arr_param['league_id']);
        $row = $this->match_model->getIntegralShooter($data);
        
        if(count($row) > 0)
        {
            $_tmp_row = $row[0];
            $_league_table = json_decode($_tmp_row['integral_data'], TRUE);
            $_shooter_list = json_decode($_tmp_row['shooter_data'], TRUE);
            
            $league_table = array();
            for($i = 0; $i < count($_league_table['item']); $i++)
            {
                $_tmp = array();
                $_tmp['order'] = $_league_table['item'][$i][0];
                $_tmp['round'] = $_league_table['item'][$i][2];
                $_tmp['team'] = $_league_table['item'][$i][1];
                
                $_data = array();
                $_data['team_name'] = $_tmp['team'];
                $_tmp['image'] = $this->match_model->getTeamLogo($_data);
                
                $_tmp['lost_count'] = $_league_table['item'][$i][5];
                $_tmp['win_count'] = $_league_table['item'][$i][3];
                $_tmp['planish_count'] = $_league_table['item'][$i][4];
                $_tmp['point'] = $_league_table['item'][$i][14];
                $league_table[] = $_tmp;
            }
            
            $shooter_list = array();
            for($i = 0; $i < count($_shooter_list['item']); $i++)
            {
                $_tmp = array();
                $_tmp['order'] = $_shooter_list['item'][$i][0];
                $_tmp['team'] = $_shooter_list['item'][$i][3];
                $_tmp['count'] = $_shooter_list['item'][$i][4];
                $_tmp['name'] = $_shooter_list['item'][$i][1];
                $shooter_list[] = $_tmp;
            }
            
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['league_table'] = $league_table;
            $arr_return['shooter_list'] = $shooter_list;
        }
        else
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        
        return $arr_return;
    }
    
    private function _get_league_list($arr_param)
    {
        $this->load->model('event/match_model');
        $arr_return = array();
        $event_id = trim($arr_param['event_id']) + 0; // 比赛类型(1为足球2为篮球) -1为不区分
        
        $data = array();
        $data['event_id'] = $event_id;
        $row = $this->match_model->getLengue($data);
        
        $arr_list = array();
        for($i = 0; $i < count($row); $i++)
        {
            $arr_list[$i]['id'] = $row[$i]['id'];
            $arr_list[$i]['event_id'] = $row[$i]['event_id'];
            $arr_list[$i]['league_name'] = $row[$i]['league_name'];
            $arr_list[$i]['league_short_name'] = $row[$i]['league_short_name'];
            $arr_list[$i]['country'] = $row[$i]['country'];
            $arr_list[$i]['grade'] = $row[$i]['grade'];
            $arr_list[$i]['team_num'] = $row[$i]['team_num'];
            $arr_list[$i]['listorder'] = $row[$i]['listorder'];
            $arr_list[$i]['band_id'] = $row[$i]['band_id'];
            $arr_list[$i]['league_logo'] = $row[$i]['logo'];
        }
         
        if(count($arr_list) == 0)
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['league_list'] = $arr_list;
        }
        
        return $arr_return;
    }
    
    private function _get_team_list($arr_param)
    {
        $this->load->model('event/match_model');
        $arr_return = array();
        $league_id = trim(@$arr_param['league_id']) + 0;
        $team_id = trim(@$arr_param['team_id']); // 可能是这种格式
        
        $data = array();
        $data['league_id'] = $league_id;
        $data['team_id'] = $team_id;
        
        $row = $this->match_model->getTeam($data);
        
        if(count($row) == 0)
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            if($team_id != '')
            {
                $arr_return['team_detail'] = $row;
            }
            else
            {
                $arr_return['team_list'] = $row;
            }
        }
        
        return $arr_return;
    }
    
    private function _get_team_detail($arr_param)
    {
        return $this->_get_team_list($arr_param);
    }
    
    private function _get_god_comment($arr_param)
    {
        $arr_return = array();
        $this->load->model('news_model');
        
        $data = array();
        $data['match_id'] = trim(@$arr_param['match_id']) + 0;
        
        $row = $this->news_model->getGodComment($data);
        
        if(count($row) == 0)
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['content'] = $row;
        }
        
        return $arr_return;
    }
    
	// 原先是根据match_id取神评，然后从神评记录的tag取相关新闻；
	// 后来改成根据match_id取球队，用球队名修为tag取新闻 2015-03-31
    private function _get_news_list($arr_param)
    {
        $arr_return = array();
        $this->load->model('news_model');
        // $this->load->model('member_model');
        $this->load->model('admin_model');
		$this->load->model('event/match_model');
        
        $data = array();
        $data['match_id'] = trim(@$arr_param['match_id']) + 0;
		$row = $this->match_model->getMatch($data['match_id']);
		if(!is_object($row))
		{
			$arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
		}
		else
		{
			$data = array();
			$data['a_name'] = $row->a_name;
			$data['b_name'] = $row->b_name;
			$data['event_name'] = $row->event_name;
			$row_real = $this->news_model->matchTeamRelationNews($data);
			for($i = 0; $i < count($row_real); $i++)
			{
				$author = $row_real[$i]['username'];
				$data = array();
				$data['username'] = $author;
				$row = $this->admin_model->get($data);
				if(count($row) > 0) // 后台用户
				{
					$row_real[$i]['author'] = $row['realname']; // 
				}
				else
				{
					
					$row_real[$i]['author'] = '无名氏';
				}
				$row_real[$i]['comment'] = substr($row_real[$i]['inputtime'], -3);
			}

			if(count($row_real) == 0)
			{
				$arr_return['state_code'] = '1';
				$arr_return['state_desc'] = '没有信息';
			}
			else
			{
				$arr_return['state_code'] = '0';
				$arr_return['state_desc'] = '成功';
				$arr_return['news_list'] = $row_real;
			}
		}
        /*
        $row = $this->news_model->getGodComment($data);
        
        if(count($row) == 0)
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $row_real = $this->news_model->newsRelationGodComment($data);
            for($i = 0; $i < count($row_real); $i++)
            {
                $author = $row_real[$i]['username'];
                $data = array();
                $data['username'] = $author;
                // $row = $this->member_model->get($data);
                $row = $this->admin_model->get($data);
                if(count($row) > 0) // 后台用户
                {
                    $row_real[$i]['author'] = $row['realname']; // 
                }
                else
                {
                    
                    $row_real[$i]['author'] = '无名氏';
                }
                $row_real[$i]['comment'] = substr($row_real[$i]['inputtime'], -3);
            }

            if(count($row_real) == 0)
            {
                $arr_return['state_code'] = '1';
                $arr_return['state_desc'] = '没有信息';
            }
            else
            {
                $arr_return['state_code'] = '0';
                $arr_return['state_desc'] = '成功';
                $arr_return['news_list'] = $row_real;
            }
        }
        */
        return $arr_return;
    }
    
	private function _get_news_detail2($arr_param)
	{
		$this->load->model('news_model');
		$news_id = $arr_param['news_id'] + 0;

        // 查文章信息
        $news = $this->news_model->getNews($news_id);
		if(is_object($news))
		{
			// 查文章详细
			$news_data = $this->news_model->getNewsData($news_id);

			// 发表时间
			$news->input_time = time_format($news->inputtime, 3);

			// 来源
			$_tmp_arr = explode('|', $news_data->copyfrom);
			$news->copy_from = $_tmp_arr[0] == '' ? '5U体育' : $_tmp_arr[0];
			$news->content =$news_data->content;
			$data = array();
			$data['news'] = $news;
			$this->load->view('article_min', $data);
		}
	}
	
    private function _get_news_detail($arr_param)
    {
        $arr_return = array();
        $this->load->model('news_model');
        $this->load->model('admin_model');
        
        $data = array();
        $data['news_id'] = trim(@$arr_param['news_id']) + 0;
        
        $obj = $this->news_model->getNews($data['news_id']);
        
        if(!is_object($obj))
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $author = $obj->username;
            $data = array();
            $data['username'] = $author;
            $row = $this->admin_model->get($data);
            if(count($row) > 0) // 后台用户
            {
                $author = $row['realname'];
            }
            else
            {
                $author = '无名氏';
            }
            $comment = substr($obj->inputtime, -3);
            
            $obj_data = $this->news_model->getNewsData($obj->id);
            
            $content = $obj_data->content;
			$content_array = array();
			
			// zhangjh 2015-03-27 分割图片和文字 
			$org = $content;
			$regex = "|(?is)<img(.*)src=\"(.*)\"|U";
			preg_match_all($regex, $org, $_tmp_arr, PREG_PATTERN_ORDER);
			
			$arr_temp = array();
			if(count(@$_tmp_arr) > 0 )
			{
				$split_content = preg_split("/<img.*\/>+/", $org);
				for($i = 0; $i < count($_tmp_arr[2]); $i++)
				{
					$arr_temp[] = array('content' => $split_content[$i], 'image' => $_tmp_arr[2][$i]);
				}
				$arr_temp[] = array('content' => $split_content[$i], 'imgae' => '');
			}
			
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['news_list'] = array('news_id' => $obj->id,
                                             'title' => $obj->title,
                                             'time' => date('Y-m-d H:i:s', $obj->inputtime),
                                             'image' => $obj->thumb,
                                             'content' => $content,
                                             'author' => $author,
                                             'comment' => $comment,
											 'content_array' => $arr_temp);
            
        }
		return $arr_return;
    }
    
    private function _edit_member_info($arr_param)
    {
        $arr_return = array();
        $this->load->model('member_model');
        
        $userid = trim(@$arr_param['userid']) + 0;
        $email = trim(@$arr_param['email']);
        $nickname = trim(@$arr_param['nickname']);
        $sex = trim(@$arr_param['sex']) + 0;
        $address = trim(@$arr_param['address']);
        $signiture = trim(@$arr_param['signiture']);
		$birthday = trim(@$arr_param['birthday']);
		$education = trim(@$arr_param['education']);
		$income = trim(@$arr_param['income']);
        
        $data = array();
        $data['gender'] = $sex;
        $data['city'] = 0;
        $data['address'] = is_gbk($address) ? arr_to_utf8($address) : $address; // api直接存字符
        $data['introduction'] = is_gbk($signiture) ? arr_to_utf8($signiture) : $signiture;
		
		$data['birthday'] = $birthday;
		$data['education'] = $education;
		$data['income'] = $income;

        $data_main = array();
        $data_main['email'] = $email;   // 这个字段在主表
        $data_main['nickname'] = is_gbk($nickname) ? arr_to_utf8($nickname) : $nickname;
        
        $row = $this->member_model->getMember($userid);
        
        if(is_object($row)) // 主表有记录
        {
            $add_message = '';
            // ======================================== 修改主表 ========================================
            if($row->email != $data_main['email'] && $data_main['email'] != '')
            {
                // Email 需要修改
                $obj_email = $this->member_model->getMemberByEmail($data_main['email']);  // 检查是否存在这个Email
                if(!is_object($obj_email))
                {
                    if(!is_email($data_main['email']))
                    {
                        $arr_return['state_code'] = '8';
                        $arr_return['state_desc'] = 'Eamil格式错误';
                        return $arr_return;
                    }
                    else
                    {
                        $add_message .= '|email:'.$row->email.'->'.$data_main['email'];
                    }
                }
                else    // 已经有这个Email
                {
                    $arr_return['state_code'] = '8';
                    $arr_return['state_desc'] = '要修改的Email已经存在';
                    return $arr_return;
                }
            }
            else
            {
                unset($data_main['email']);
            }
            
            if($row->nickname != $data_main['nickname'] && $data_main['nickname'] != '')
            {
                $obj_nickname = $this->member_model->getMemberByNickName($data_main['nickname']);
                if(!is_object($obj_nickname))
                {
                        $add_message .= '|nickname:'.$row->nickname.'->'.$data_main['nickname'];
                   
                }
                else    // 已经有这个nickname
                {
                    $arr_return['state_code'] = '8';
                    $arr_return['state_desc'] = '要修改的Nickname已经存在';
                    return $arr_return;
                }
            }
            else
            {
                unset($data_main['nickname']);
            }
            
                    
            if(count($data_main) > 0) // 可以修改主表的Email
            {
                $this->member_model->editMember($userid, $data_main);
                $main_talbe_modify = TRUE;
            }
            else
            {
                $main_talbe_modify = FALSE;
            }
            
            // ======================================== 修改附表 ========================================
            $old = $this->member_model->getMemberDetail($userid); // 附表旧数据
            if(is_object($old)) // 附表有记录
            {
                if($old->gender != $data['gender'] || $old->city != $data['city'] || $old->introduction != $data['introduction'] || 
				   $old->address != $data['address'] || $old->birthday != $data['birthday'] || $old->education != $data['education'] || 
				   $old->income != $data['income'])
                {
                    if($old->gender != $data['gender']) // 这个可以是0
                    {
                        $add_message .= '|gender:'.$old->gender.'->'.$data['gender'];
                    }
                    else    // 如果是空的，不处理
                    {
                        unset($data['gender']);
                    }
                    
                    if($old->city != $data['city'] && $data['city'] > 0)
                    {
                        $add_message .= '|city:'.$old->city.'->'.$data['city'];
                    }
                    else
                    {
                        unset($data['city']);
                    }
                    
                    if($old->introduction != $data['introduction'] && $data['introduction'] != '')
                    {
                        $add_message .= '|introduction:'.$old->introduction.'->'.$data['introduction'];
                    }
                    else
                    {
                        unset($data['introduction']);
                    }
                    
                    if($old->address != $data['address'] && $data['address'] != '')
                    {
                        $add_message .= '|address:'.$old->address.'->'.$data['address'];
                    }
                    else
                    {
                        unset($data['address']);
                    }
					
					if($old->birthday != $data['birthday'] && $data['birthday'] != '') {
                        $add_message .= '|birthday:'.$old->birthday.'->'.$data['birthday'];
                    } else {
                        unset($data['birthday']);
                    }
					
					if($old->education != $data['education'] && $data['education'] != '') {
                        $add_message .= '|education:'.$old->education.'->'.$data['education'];
                    } else {
                        unset($data['education']);
                    }
					
					if($old->income != $data['income'] && $data['income'] != ''){
                        $add_message .= '|income:'.$old->income.'->'.$data['income'];
                    } else {
                        unset($data['income']);
                    }

                    if($add_message == '') // 没有要修改的内容
                    {
                        $_arr_return = array('success' => FALSE, 'message' => '没有要修改的内容');
                    }
                    else
                    {
                        if(count($data) > 0) // 可以修改附表
                        {
                            $this->member_model->editMemberDetail($userid, $data);
                        }

                        $_arr_return = array('success' => TRUE, 'message' => '修改成功');
                    }
                }
                else
                {
                    $_arr_return = array('success' => FALSE, 'message' => '没有要修改的内容');
                }
            }
            else    // 附表无记录
            {
                $add_message .= '|gender:'.$data['gender'];
                $add_message .= '|city:'.$data['city'];
                $add_message .= '|introduction:'.$data['introduction'];
                $add_message .= '|address:'.$data['address'];
				$add_message .= '|birthday:'.$data['birthday'];
				$add_message .= '|education:'.$data['education'];
				$add_message .= '|income:'.$data['income'];
                
                $data['userid'] = $userid;
                $this->member_model->setMemberDetail($data);
                
                $_arr_return = array('success' => TRUE, 'message' => '修改成功');
            }
            
            if($_arr_return['success'] == TRUE || $main_talbe_modify)   // 主表或附表有更新的
            {
                $arr_return['state_code'] = '0';
                $arr_return['state_desc'] = '成功';
                passport_write_log($userid, '用户编辑资料|api'.$add_message, '修改成功');
            }
            else
            {
                //$arr_return['state_code'] = '0';
                //$arr_return['state_desc'] = '成功';
                $arr_return['state_code'] = '7';
                $arr_return['state_desc'] = '没有要修改的内容';
                passport_write_log($userid, '用户编辑资料|api'.$add_message, '没有要修改的内容');
            }
        }
        else
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
         
         return $arr_return;
    }
    
    private function _search_member($arr_param)
    {
        $arr_return = array();
        // strpos() examples
        $member_list = array();
        $this->load->model('member_model');
        
        $data = array();
        $data['gender'] = $arr_param['sex'] + 0;
        $data['ageMin'] = $arr_param['age_min'] + 0;
        $data['ageMax'] = $arr_param['age_max'] + 0;
        $data['userName'] = trim($arr_param['username']);
        
        $row = $this->member_model->getMemberByAgeGender($data);
        for($i = 0; $i < count($row); $i++)
        {
            // 取其他信息
            $arr_param = array();
            $arr_param['userid'] = $row[$i]['userid'] + 0;
            $_arr_member = $this->_get_member($arr_param);
            
            if($_arr_member['state_code'] == '0')
            {
                $_arr = array();
                $_arr['userid'] = $_arr_member['user_detail'][0]['userid'];
                $_arr['username'] = $_arr_member['user_detail'][0]['username'];
                $_arr['logo'] = $_arr_member['user_detail'][0]['logo'];
                $_arr['email'] = $_arr_member['user_detail'][0]['email'];
                $_arr['nickname'] = $_arr_member['user_detail'][0]['nickname'];
                $_arr['sex'] = $_arr_member['user_detail'][0]['sex'];
                $_arr['address'] = $_arr_member['user_detail'][0]['address'];
                $_arr['signiture'] = $_arr_member['user_detail'][0]['signiture'];
                $_arr['birthday'] = $row[$i]['birthday'];
                $_arr['age'] = $row[$i]['age'];
				$_arr['mobile_phone'] = $_arr_member['user_detail'][0]['mobile_no'];
				$_arr['bind_email'] = $_arr_member['user_detail'][0]['bind_email'];
				$_arr['bind_mobile'] = $_arr_member['user_detail'][0]['bind_mobile'];
				$_arr['education'] = $_arr_member['user_detail'][0]['education'];
				$_arr['income'] = $_arr_member['user_detail'][0]['income'];
				$_arr['album'] = passport_get_discuz_album($_arr_member['user_detail'][0]['userid']);
				
                $member_list[] = $_arr;
            }
        }
        
        if(count($member_list) == 0)
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        else
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '成功';
            $arr_return['search_list'] = $member_list;
        }
        return $arr_return;
    }
    
    private function _change_password($arr_param)
    {
        $arr_return = array();
        $data = array();
        $data['userid'] = $arr_param['userid'] + 0;
        $data['from'] = 'api';
        $data['old_pwd'] = $arr_param['oldpwd'];
        $data['new_pwd'] = $arr_param['newpwd'];
        
        $return = passport_change_password($data);
        
        if($return['success'] === TRUE)
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '修改密码成功';
        }
        else
        {
            $arr_return['state_code'] = '8';
            $arr_return['state_desc'] = '修改密码失败|'.$return['message'];
        }
        return $arr_return;
    }
    
    private function _find_password($arr_param, $bln_only_email = FALSE)
    {
        $arr_return = array();
        $data = array();
        $data['username'] = trim(@$arr_param['account']);	// 如果 bln_only_email == TRUE，这个可能没有传
        $data['from'] = 'api';
        $data['email'] = trim(@$arr_param['email']);
        
        $return = passport_find_password($data, $bln_only_email);
        
        if($return['success'] === TRUE)
        {
            $arr_return['state_code'] = '0';
            $arr_return['state_desc'] = '已经将修改密码链接发送至您的邮箱';
        }
        else
        {
            $arr_return['state_code'] = '8';
            $arr_return['state_desc'] = '操作失败|'.$return['message'];
        }
        return $arr_return;
    }
    
	// 绑定新手机号
	private function _bind_phone($arr_param)
	{
		$arr_return = array();
		
		// 1,先验证token，verify_code是否正确
		$action = 'bind_mobile_2';
		$data = array();
		
		$data['token'] = $arr_param['token'];
		$data['verify_code'] = $arr_param['verify_code'];
		$tmp_return = $this->_action($action, $data);
		if($tmp_return['state_code'] == '0') // 校验通过
		{
			$this->load->model('member_model');
			$mobile = $tmp_return['userinfo'][0]['mobile'];
			$userid = $arr_param['userid'] + 0;
			
			$data = array();
			$data['mobile_no'] = $mobile;
			$data['bind_mobile'] = 1;
			
			$old = $this->member_model->getMemberDetail($userid);
			if(is_object($old))
			{
				if($old->mobile_no != $data['mobile_no'] || $old->bind_mobile != $data['bind_mobile'])
				{
					$add_message = '|mobile_no:'.$old->mobile_no.'->'.$data['mobile_no'];
					$add_message .= '|bind_mobile:'.$old->bind_mobile.'->1';
					$this->member_model->editMemberDetail($userid, $data);
					passport_write_log($userid, '用户手机设置|passport'.$add_message, '修改成功');
					$_return = array('success' => TRUE, 'message' => '修改成功');
				}
				else
				{
					$_return = array('success' => TRUE, 'message' => '没有要修改的内容');
				}
			}
			else
			{
				$add_message = '';
				$add_message .= '|mobile_no:'.$data['mobile_no'];
				$add_message .= '|bind_mobile:1';
				$this->member_model->setMemberDetail($userid, $data);
				passport_write_log($userid, '用户手机设置|passport'.$add_message, '录入成功');
				$_return = array('success' => TRUE, 'message' => '修改成功');
			}
			
			if($_return['success'] === TRUE)
			{
				$arr_return['state_code'] = '0';
				$arr_return['state_desc'] = '修改成功';
			}
			else
			{
				$arr_return['state_code'] = '8';
				$arr_return['state_desc'] = '操作失败|'.$_return['message'];
			}
		}
		else
		{
			$arr_return['state_code'] = $tmp_return['state_code'];
			$arr_return['state_desc'] = $tmp_return['state_desc'];
		}
		
		return $arr_return;
	}
	
    private function _bind_email($arr_param)
    {
        $arr_return = array();
        
        $this->load->model('member_model');
        $data = array();
		if(trim(@$arr_param['userid']) + 0 > 0)
		{
			$data = array('userid' => $arr_param['userid']);
		}
		else
		{
			$data = array('username' => $arr_param['account']);
		}
        $row = $this->member_model->get($data);
        if(count($row) > 0)
        {
			$arr_return['state_code'] = '';
			$arr_return['state_desc'] = '';
			$email = trim($arr_param['email']);
			if(trim(@$arr_param['userid']) + 0 > 0)	// 检查密码是否正确
			{
				if($row['password'] == md5(md5(trim(@$arr_param['password'])).$row['encrypt']) || $arr_param['not_need_pwd'] == 1)
				{
					// OK
				}
				else
				{
					$arr_return['state_code'] = '8';
					$arr_return['state_desc'] = '操作失败|密码不正确';
				}
			}
			
			if($arr_return['state_desc'] == '')
			{
				// 如果是第二步绑定Email，只需要验证是否 userid 是否和email匹配，无需使用 passport_email_can_register
				if($arr_param['not_need_pwd'] == 1 && $row['email'] == $email)
				{}
				else
				{
					$pass = passport_email_can_register($email);
					if($pass['success'] === TRUE)
					{
					}
					else
					{
						$arr_return['state_code'] = '8';
						$arr_return['state_desc'] = '操作失败|'.$pass['message'];
					}
				}
				
			}
			
            if($arr_return['state_desc'] == '')
            {
                $add_message = '|email:'.$row['email'].'->'.$email;
                
                $param = array();
                $param['action'] = 'bind_email';
                $param['url_head'] = $this->config->item('home_url').'/passport/email/confirm';
                $param['userid'] = $row['userid'] + 0;
                $param['email'] = $email;
                $confirm_url = passport_mail_option_url($param);
                
                $email_result = passport_send_mail($email, 3, $confirm_url);
                passport_write_log($param['userid'], '用户邮箱绑定申请|api'.$add_message, $email_result);
                $arr_return = array('state_code' => '0', 'state_desc' => '邮件已发送，请确认');
            }
        }
        else
        {
            $arr_return['state_code'] = '1';
            $arr_return['state_desc'] = '没有信息';
        }
        
        return $arr_return;
    }
    
    private function _edit_member_avatar($arr_param)
    {
        $arr_return = array();
        $userid = $arr_param['userid'] + 0;
        $input = trim($arr_param['input']);
        if($input == '')
        {
            $arr_return['state_code'] = '4';
            $arr_return['state_desc'] = '参数不足';
        }
        else
        {
            $result = passport_avatar_create($userid, $input);
            if($result === TRUE)
            {
                $arr_return['state_code'] = '0';
                $arr_return['state_desc'] = '成功';
            }
            else
            {
                $arr_return['state_code'] = '8';
                $arr_return['state_desc'] = '操作失败|'.$result;
            }
        }
        return $arr_return;
    }
	
	private function _watch_file_log($arr_param)
	{
		$arr_return = array();
		$type = $arr_param['type'];
		if($type == 'edit_member_avatar')
		{
			echo "<pre>";
			watch_file_log('liaoqiu', 'log_error', 100, date('Ymd'));
		}
	}
	
	private function _register_oauth($arr_param)
	{
		$arr_return = array();
		$type = $arr_param['type'] + 0;
		$email = trim(@$arr_param['email']);
		$token = trim(@$arr_param['token']);
		$password = trim(@$arr_param['password']);
		$from = trim(@$arr_param['from']);
		$connectid = trim(@$arr_param['connectid']);
		$verify_code = trim(@$arr_param['verify_code']);
		$remote_avatar = trim(urldecode($arr_param['remote_avatar']));
		
		if($email == '' && $token == '')
		{
			$arr_return['state_code'] = '4';
			$arr_return['state_desc'] = '参数不足';
		}
		else
		{
			if($type == 1)	// 1. 邮箱注册
			{
				$data = array();
				$data['email'] = $email;
				$data['password'] = $password;
				$data['from'] = $from;
				$data['connectid'] = $connectid;
				$data['remote_avatar'] = $remote_avatar;
				$action = 'register_email_1';
				$arr_return = $this->_action($action, $data);
			}
			elseif($type == 2)	// 2. 手机注册
			{
				// 先验证verify_code
				if($verify_code != '')
				{
					$data = array();
					$data['token'] = $token;
					$data['verify_code'] = $verify_code;
					$action = 'register_mobile_2';
					$arr_return = $this->_action($action, $arr_param); // 先验证verify_code
					if($arr_return['state_code'] == '0')
					{
						$data = array();
						$data['token'] = $token;
						$data['password'] = $password;
						$data['from'] = $from;
						$data['connectid'] = $connectid;
						$data['remote_avatar'] = $remote_avatar;
						$action = 'register_mobile_3';
						$arr_return = $this->_action($action, $data);
					}
				}
				else
				{
					$arr_return['state_code'] = '4';
					$arr_return['state_desc'] = '参数不足';
				}
			}
		}
		return $arr_return;
	}
	
	private function _login_oauth($arr_param)
	{
		$this->load->model('linkage_model');
		$this->load->model('member_model');
		
		$arr_return = array();
		$arr_member = passport_get_member_by_oauth($arr_param);
		$from = trim($arr_param['from']);
		$connectid = trim($arr_param['connectid']);
		
		if(count($arr_member) == 1)
		{
			$arr_return['state_code'] = '0';
			$arr_return['state_desc'] = '登录成功';
			$row = $arr_member[0];
			
			$arr_data = array(
				'lastdate' => time(),
				'lastip' => ip(),
				'loginnum' => $row['loginnum'] + 1
			);
			
			$this->member_model->update($arr_data, $row['userid']);
			
			$logo_url = getmemberucavatar($row['phpssouid'] + 0);
			//$passport_avatar_exist = passport_avatar_exist($row['userid'], 'middle');
			//if($passport_avatar_exist === TRUE)
			//{
				$logo_url = passport_avatar_show($row['userid'], 'middle', TRUE);
			//}
			
			$arr_return['state_code'] = '0';
			$arr_return['state_desc'] = '成功';
			$arr_return['username'] = $row['username'];
			$arr_return['userid'] = $row['userid'];
			$arr_return['member_logo'] = $logo_url;
			$arr_return['nick_name'] = trim(@$row['nickname']);
			$arr_return['email'] = $row['email'];
			
			$sex = trim(@$arr_param['sex']) + 0;
			$address = trim(@$arr_param['address']);
			$signiture = $row['userid'];
			
			$user_detail = $this->member_model->getMemberDetail($row['userid']);
			if(is_object($user_detail))
			{
				$arr_return['sex'] = $user_detail->gender == '' ? '0' : $user_detail->gender;
				
				$address_text = trim($user_detail->address);
				if($address_text == '')
				{
					$arr_return['address'] = $this->linkage_model->getAddress($user_detail->city);
				}
				else
				{
					$arr_return['address'] = $address_text;
				}
				
				$arr_return['signiture'] = $user_detail->introduction;
				$arr_return['mobile_phone'] = $user_detail->mobile_no;
				$arr_return['bind_email'] = $user_detail->bind_email;
				$arr_return['bind_mobile'] = $user_detail->bind_mobile;
				$arr_return['album'] = passport_get_discuz_album($row['userid']);
				$arr_return['birthday'] = $user_detail->birthday;
				$arr_return['education'] = $user_detail->education;
				$arr_return['income'] = $user_detail->income;
			}
			else
			{
				$arr_return['sex'] = '0';
				$arr_return['address'] = '';
				$arr_return['signiture'] = '';
				$arr_return['mobile_phone'] = '';
				$arr_return['bind_email'] = '';
				$arr_return['bind_mobile'] = '';
				$arr_return['album'] = '';
				$arr_return['birthday'] = '';
				$arr_return['education'] = '';
				$arr_return['income'] = '';
			}
			passport_write_log($row['userid'], '用户登录|'.$from, '登录成功');
		}
		elseif(count($arr_member) > 1)
		{
			$arr_return['state_code'] = '8';
			$arr_return['state_desc'] = '操作失败|账号信息有重复';
			passport_write_log(0, '用户登录|'.$from, '登录失败|connectid重复'.$connectid);
		}
		else
		{
			$arr_return['state_code'] = '-99';
			$arr_return['state_desc'] = '无会员记录，需要新注册';
			passport_write_log(0, '用户登录|'.$from, '登录失败|没有信息');
		}
		
		return $arr_return;
	}


	/**   
	 * [联赛Api]
	 * 
	 * [获取所需要的全部联赛]  /basis/getAllLeague
	 * [根据ID获取单个赛程详情]  /basis/getOneLeague?id=1
	 * 
	 * 
	 *  返回 state_code 0 成功
     *                 1 没有信息
     *                 2 sign验证错误
     *                 3 参数错误
     *                 4 参数不足
	 */
	public function basis($act,$sign=''){
		//if($sign==SIGN){
			$arr = array();
			switch ($act) {
				case 'getAllLeague':  	//根据时间排序，获取全部赛程				
					$this->load->model('basis_model');
					$arr = $this->basis_model->getAllLeague();				
					break;
				case 'getOneLeague':  	//根据ID获取单个赛程详情
					if($id=$this->input->get('id')){
						$this->load->model('basis_model');
						$arr = $this->basis_model->getOneLeague($id);	
					}								
					break;
				case 'getPlayer':  	//根据时间排序，获取全部赛程
					if($id=$this->input->get('id')){
						$this->load->model('basis_model');
						$arr = $this->basis_model->getPlayer($id);	
					}											
					break;
				case 'getTeamPlayer':  	//根据时间排序，获取全部赛程
					if($id=$this->input->get('id')){
						$this->load->model('basis_model');
						$arr = $this->basis_model->getPlayer($id);	
					}											
					break;		
				default:
					
					break;
			}
			
			
			if($arr){  //如果有数据，统一处理		
				$arr_return['state_code'] = '0'; 
	            $arr_return['state_desc'] = '成功';
	            $arr_return['content'] = $arr;					
	        }else{
	            $arr_return['state_code'] = '1'; 
	            $arr_return['state_desc'] = '没有信息';
	        }
		/*}
		else{//IF sign 
					$arr_return['state_code'] = '2'; 
					$arr_return['state_desc'] = 'SIGN鍊间笉姝ｇ‘';
				}*/
		
		
		echo json_encode($arr_return);
		dump($arr_return);
	}


}

?>