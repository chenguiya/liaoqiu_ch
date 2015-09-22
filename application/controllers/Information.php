<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Information extends CI_Controller {

	private $res = array(
		"state_code"	=>	-99,
		"state_desc"	=>	"非法提交",
	);


	/**
	 * 构造函数
	 */
	public function __construct()
	{
	    parent::__construct();
        $this->load->helper('huanxin_helper');
        verify_sign();
	}

	//获取联赛列表接口
	public function getleaguelist($event_id) {
		//判断参数完整性
		if(!isset($event_id)) output($this->res);
		if(!is_num($event_id)) {
			$this->res["state_desc"] = "event_id错误";
			output($this->res);
		}
		$params = array();
		$params["event_id"] = $event_id;
		$result_5u_api = json_decode(usport_api("get_league_list", $params), true);
                //过滤数据
                if(empty($result_5u_api['state_code']))
                {
                    $allows = array(
                      '英超','西甲','意甲','法甲','德甲','中超','国家队', '热门'
                    );
                    
                    $sort_id = array();
                    //热门球队
                    $hot_league = array(
                      'id' => "0",
                       'event_id' => "1",
                        "league_name" => "热门",
                        "league_short_name" => "热门",
                        "country" => null,
                        "grade" => null,
                        "team_num" => null,
                        "listorder" => 0,
                        "band_id" => 0,
                        "league_logo" => null,
                    );
                    
                    array_unshift($result_5u_api['league_list'], $hot_league);
                    foreach ($result_5u_api['league_list'] as $key => $row)
                    {
                        if(!in_array($row['league_short_name'], $allows))
                        {
                            unset($result_5u_api['league_list'][$key]);
                        }
                        
                    }
                    
                    $result_5u_api['league_list'] = array_values($result_5u_api['league_list']);
                }
		output($result_5u_api);
	}
	
	//获取球队列表接口
	public function getteamlist($league_id) {
		//判断参数完整性
		if(!isset($league_id)) {
			$this->res["state_code"] = -99;
			$this->res["state_desc"] = "参数错误";
			output($this->res);
		}

		if(!is_num($league_id)) {
			$this->res["state_code"] = -99;
			$this->res["state_desc"] = "event_id错误";
			output($this->res);
		}

                if(empty($league_id))
                {
                    $this->load->config('hot_league');
                    $hot = $this->config->item('hot');
                    $hot = array_values($hot);
                    $result_5u_api = array(
                        'state_code' => '0',
                        'state_desc' => '成功',
                        'team_list' => !empty($hot) ? $hot : array(),
                    );
                    
                    
                }
                else 
                {

                    $params = array();
                    $params["league_id"] = $league_id;
                    $result_5u_api = json_decode(usport_api("get_team_list", $params), true);

                }
		output($result_5u_api);
	}
	
	//获取联赛及联赛下的各球队列表接口
	public function getleagueandteamlist($event_id) {
		//判断参数完整性
		if(!isset($event_id)) output($this->res);
		if(!is_num($event_id)) {
			$this->res["state_desc"] = "event_id错误";
			output($this->res);
		}

		$params = array();
		$params["event_id"] = $event_id;
		$result_5u_api = json_decode(usport_api("get_league_list", $params), true);

        foreach ($result_5u_api["league_list"] as $k => $v) {
            $params = array("league_id"=>$v["id"]);
            $result = json_decode(usport_api("get_team_list", $params), true);
            @$result_5u_api["league_list"][$k]["team_list"] = $result["team_list"];
        }

		output($result_5u_api);
	}

	//设置关注的球队
	public function setfocusteam($token, $team) {
        $team = urldecode($team);
		//判断参数完整性
		if(!isset($token) || !isset($team)) output($this->res);

		if($team != "NULL"){
            //校验team字符串
            $team = deal_width_symbol($team, ",");
        } else {
            $team = "";
        }
		//校验token, 并顺便取用户基本信息
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];
		$params = array();
		$params["member_id"] = $member_id;
		$params["focusteam"] = $team;
		$this->load->model('liaoqiu_focusteam_model');
		$focus = $this->liaoqiu_focusteam_model->getByMemberID($member_id);
		$focus ? $this->liaoqiu_focusteam_model->editByMemberID($focus['member_id'], $params) : $this->liaoqiu_focusteam_model->setRecord($params);
		$this->res["state_code"] = 0;
		$this->res["state_desc"] = "成功";
		output($this->res);
	}

    //获取关注球队
    public function getfocusteam($token) {
        //判断参数完整性
		if(!isset($token)) output($this->res);
		//校验token, 并顺便取用户基本信息
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];
        $this->load->model('liaoqiu_focusteam_model');
		$focus_team_arr = $this->liaoqiu_focusteam_model->getByMemberID($member_id);
        if(count($focus_team_arr)==0){
            $this->res["state_code"] = 0;
			$this->res["state_desc"] = "尚未设置关注球队";
			output($this->res);
        }
        
        $this->getteamdetail($focus_team_arr["focusteam"]);
    }	
	
   //获取地区列表
    public function getarealist() {
    	//$this->load->library('memcache');
    	//$arr_new = $this->memcache->get('lq_arealist');
		//if(empty($arr_new)){
			        $url = "http://api.5usport.com/ajax/city/data";
			        $ch = curl_init();
			        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			        curl_setopt($ch, CURLOPT_URL, $url);
			        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			        curl_setopt($ch, CURLOPT_HTTPGET, true);
			        $str = curl_exec($ch);
			        $err = curl_error($ch);
			        curl_close($ch);
			        if ($err) {
						$this->res["state_desc"] = "获取地区失败".$err;
						output($this->res);
			        }
			        if(trim($str)==""){
						$this->res["state_desc"] = "获取地区失败";
						output($this->res);
			        }
			        $arr_area = json_decode($str, true);
			        $arr_new = array();
			        foreach ($arr_area["citylist"] as $k => $v) {
			            $arr_new[$k]["name"] = $v["p"];
			            if(is_array($v["c"])){
			                foreach ($v["c"] as $k1 => $v1) {
			                    $arr_new[$k]["list"][$k1]["name"] = $v1["n"];
			                    if(array_key_exists("a", $v1)){
			                        foreach ($v1["a"] as $k2 => $v2) {
			                            $arr_new[$k]["list"][$k1]["list"][$k2]["name"] = $v2["s"];
			                        }
			                    }
			                }
			            }
			        }
		//			$this->memcache->set('lq_arealist',$arr_new,0,3600*24*30);
		//}
        $this->res["state_code"] = 0;
		$this->res["state_desc"] = "成功";
        $this->res["area_list"] = $arr_new;
		output($this->res);
    }
	

	//获取球队详情接口
	public function getteamdetail($team_id) {
		$team_id = urldecode($team_id);
		//判断参数完整性
		if(!isset($team_id)) output($this->res);
		//校验team字符串
		$team_id = deal_width_symbol($team_id, ",");
		$params = array("team_id"=>$team_id);
		$result_5u_api = json_decode(usport_api("get_team_detail", $params), true);
		output($result_5u_api);
	}

	//获取比赛技术统计、比赛事件
    public function getmatchdata($match_id) {
        //判断参数完整性
		if(!isset($match_id)) output($this->res);
        $params = array("match_id"=>$match_id);
		//step2: 通过接口，获取赛程。
		$result_5u_api = json_decode(usport_api("get_match_data", $params), true);
		output($result_5u_api);
    }

	//获取积分榜,射手榜接口
	public function getranklistbyleague($league) {
		//判断参数完整性
		if(!isset($league)) output($this->res);

		if(!is_num($league)) {
			$this->res["state_desc"] = "league错误";
			output($this->res);
		}

		$params = array("league_id"=>$league);

		//step2: 通过接口，获取赛程。
		$result_5u_api = json_decode(usport_api("get_match_integral_shooter", $params), true);
		if($result_5u_api["state_code"]!="0") {
			$this->res["state_code"] = $result_5u_api["state_code"];
			$this->res["state_desc"] = $result_5u_api["state_desc"];
			output($this->res);
		}
		$arr_league_table = $result_5u_api["league_table"];
		$arr_shooter_list = $result_5u_api["shooter_list"];

		$this->res["state_code"] = 0;
		$this->res["state_desc"] = "成功";
		$this->res["league_table"] = $arr_league_table;
		$this->res["shooter_list"] = $arr_shooter_list;

		output($this->res);
	}

        /**
         * 获取主播列表
         * 
         */
	public function get_anchors()
        {
            $result = array('state_code' => -99, 'state_desc' => '非法提交');
            
            try 
            {
                load_model('member');
                $anchors = $this->liaoqiu_member_model->getMemberList(array('role' => Liaoqiu_member_model::ROLE_ANCHOR));
                $return = array();
                
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
                        //$row['member_logo'] = passport_avatar_show($member_id, 'middle', TRUE);
                        $return[] = $row;
                    }
                    
                    unset($anchors);
                }
                
                $result['state_code'] = 0;
                $result['state_desc'] = '成功';
                $result['anchors_list'] = array_values($return);
            } 
            catch (Exception $ex) 
            {
                $result['state_code'] = $ex->getCode();
                $result['state_desc'] = $ex->getMessage();   
            }
            
            output($result);
        }
        
 	//获取阵容数据
	public function getteamformation($match_id) {
        $match_id = urldecode($match_id);
		//判断参数完整性
		if(!isset($match_id)) {
			$this->res["state_code"] = -99;
			$this->res["state_desc"] = "参数错误";
			output($this->res);
		}

        $params = array();
		$params["match_id"] = $match_id;
        $params["direction"] = "vertical";
		$result_5u_api = json_decode(usport_api("get_match_formation", $params), true);
		output($result_5u_api);
	}       
        

    /**
     * 获取主播用户的详细信息
     * 
     */
//    public function getanchordetail() {
//        //会员ID，可为空
//        $token = $this->input->get('token', true);
//        //主播ID
//        $member_id = $this->input->get('member_id', true);
//        //判断参数完整性
//        if( !isset($member_id) || empty($member_id)) 
//        {
//            $this->res["state_code"] = -99;
//            $this->res["state_desc"] = "参数错误";
//            output($this->res);
//        }
//        
//        //先校验令牌token, 并获取用户信息
//        if(!empty($token))
//        {
//            $return_lq_member = getuser_by_token($token);
//        }
//        
//        //是主播
//        load_model('member');
//        $member = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
//        if(empty($member) || Liaoqiu_member_model::ROLE_ANCHOR != $member['role']|| Liaoqiu_member_model::STATUS_USABLE != $member['status'] )
//        {
//            //output($this->res);
//        }
//        
//        //校验用户账号列表合法性
//        $arr_user_list = getinfo_byuserid($member_id);
//        
//        if(!empty($arr_user_list['state_code']) || empty($arr_user_list['user_detail']))
//        {
//            output($this->res);
//        }
//        $arr_user_list = $arr_user_list["user_detail"];
//        $arr_user_list = add_account_hx_username($arr_user_list);
//                
//        foreach ($arr_user_list as $k => $v) {
//            //Do something here.
//            $member_id = $v['member_id'];
//            $v["role"] = Liaoqiu_member_model::ROLE_ANCHOR;
//           
//           //粉丝数
//            $v['fans_count'] = get_fans_count($v['member_id']);
//            //关注数:
//            $v['follows_count'] = get_follows_count($v['member_id']);
//            //爱队：
//            $v['focusteam'] = get_focusteam($v['member_id']);
//            //相册
//            $v['album'] = get_album($v['member_id']);
//
//            $arr_result[] = $v;
//        }
//        
//        $this->res["state_code"] = 0;
//        $this->res["state_desc"] = "成功";
//        $this->res["user_list"] = $arr_result;
//        output($this->res);
//    }
//    
}
?>	