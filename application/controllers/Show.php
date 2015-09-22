<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Show extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "非法提交",
    );

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
		$this->load->model('liaoqiu_show_model');
		$this->load->model('liaoqiu_member_model');
		$this->load->model('liaoqiu_show_reserve_model');
		$this->load->library('MY_Memcached');
       	verify_sign();
		
    }
	
	//获取节目列表,/令牌/时间戳，20条/时间类型1往前查，2往后查/节目类型(1热门2话题3联赛4预约)/类型id
	public function getshowlist($token,$now_time='NULL',$time_type='NULL',$type,$type_id)
	{
		$token = urldecode($token);
        $now_time = urldecode($now_time);
		$time_type = urldecode($time_type);
		$type = urldecode($type);
		$type_id = urldecode($type_id);
		if(empty($token) && empty($now_time) && empty($time_type) && empty($type) && empty($type_id)) output($this->res); 
		if(isset($_GET['type']) && ($_GET['type'] == 1 ||  $_GET['type'] == 2)) $where = 'type='.$_GET['type'];
		
		if($token != 'NULL'){
				$member = getuser_by_token($token);
				$member_id = $member["member_id"];
		}		
		
		if($type != 'NULL'){
			switch ($type) {
				case '1'://热门，默认
					break;
				case '2'://话题直播列表
					$where = array('status'=>1,'type'=>2,'link_id'=>$type_id);
					break;
				case '3'://联赛直播列表
					$where = array('status'=>1,'type'=>1,'league_id'=>$type_id);
					break;
				case '4'://预约直播列表
					if($token == 'NULL' || !isset($token)){
					     $this->res["state_desc"] = "缺少token";
           				 output($this->res);
					}
					$list = $this->liaoqiu_show_model->get_reserve_by_uid($member_id);
					break;		
                //定制节目（主播）
                case '5':
                    $list = $this->get_customer_anchors($member_id);
                    break;
                //定制节目（球队）
                case '6':
                    $list = $this->get_customer_team($member_id);
                    break;
                //回顾
                case '7':
                    $list = $this->get_member_history($member_id);
                    break;
                //根据主播id获取节目列表
                case '8':
					$where = "zhubo1 = $type_id or zhubo2 = $type_id";
					$now_page = isset($_GET['page'])?trim($_GET['page']):1;
                    $list = $this->base_model->page(10,$now_page,'show',$where);
                    break;
					                                
				default:
					$this->res["state_desc"] = "type类型错误";
					output($this->res);
					break;
			}
		}
		$arr_return = array("state_code"=>0,"state_desc"=>"成功",'show_list'=>array()); 
		
		if(!in_array($type, array(4, 5 , 6, 7,8))){  //不是 预约直播列表
				if($type_id == 'NULL' && $type != 'NULL' ){
					     $this->res["state_desc"] = "缺少type_id";
           				 output($this->res);
				}
				$list_num =10;  //默认加载数据条数
				if($now_time == 'NULL'){
					$now_time = time();
				}
				if($time_type == '1'){
					$where['end_time <'] = $now_time;  //往前查询
					$order = 'desc';   //往前查询,顺序
				}else if($time_type == '2'){
					$where['end_time >'] = $now_time;  //往后查询
					$order = 'asc';  //往后查询,倒序
				}else{  //第一次加载
					$where['end_time >'] = $now_time;  //往后查询
					$order = 'asc';  //往后查询,顺序
					$new_list = $this->liaoqiu_show_model->get_list_by_time($where,$order,$list_num);  //限制10条
					$count = count($new_list) - $list_num;  //如果为负数，则新数据不满10条，继续加载旧的数据
					if($count < 0){  //继续加载旧的数据
							unset($where['end_time >']);
							$where['end_time <'] = $now_time;  //往后查询
							$order = 'desc';  //往前查询,倒序
							$old_list = $this->liaoqiu_show_model->get_list_by_time($where,$order,abs($count));  //绝对值，凑够十条
							krsort($old_list);   //降序
					}//if($count < 0)
					$list = isset($old_list)?array_merge($old_list,$new_list):$new_list;				
				}//if($time_type == '1')
				if(!isset($new_list)) $list = $this->liaoqiu_show_model->get_list_by_time($where,$order,$list_num);  //限制10条
				$arr_return["show_nav"]=$this->shownav();  //顶部导航
		}// if(!in_array($type
		$show_list = array();
		$result = array();
		if(!empty($list)){
			$topic_arr = $this->get_topic_type();
			$id_str = '';
			foreach ($list as $k=>$v) {
				if($v['end_time']<time()){  //节目状态
						$v['show_status'] = 0;// 0已结束 
				}else if($v['start_time']<=time() && $v['end_time']>=time()){
						$v['show_status'] = 1;// 1直播中 
				}else{
						if($token != 'NULL') $check_reserve = $this->liaoqiu_show_reserve_model->getRecordByshowIdUid($v['id'],$member_id);
						$v['show_status'] = !empty($check_reserve) ? 2 : 3;// 2已预约 3预约
				} 
				$uid_str = '';
				$uid_str .= $v['zhubo1'].",".$v['zhubo2'];  //一次性获取主播信息
				$zhuobo = $this->liaoqiu_member_model->getMemberByManyMemberID($uid_str);
				$v['zhubo'] = $zhuobo;
				$v['premier_status'] = "-1";// -1 非听英超
				if(!empty($v['premier_url'])){
					if($v['premier_end_time']<time()){  //节目状态
							$v['premier_status'] = 0;// 0已结束 
					}else if($v['premier_start_time']<=time() && $v['premier_end_time']>=time()){
							$v['premier_status'] = 1;// 1未开始
					}else{
							$v['premier_status'] = 2;// 2直播中
					}// if					
				}//if(!empty($v['premier_url']))				                               
                //场控
                $v['changkong'] = array();                
                $uid_str = '';
                if(!empty($v['changkong1']) || !empty($v['changkong2']))
                {
                    $uid_str .= $v['changkong1'] . "," . $v['changkong2'];
                    $uid_str = trim($uid_str, ',');
                }
                if(!empty($uid_str))
                {

                    $changkong = $this->liaoqiu_member_model->getMemberByManyMemberID($uid_str);
                    $v['changkong'] = $changkong;
                }
				if($v['type'] == 1){
					$id_str .= $v['link_id'].","; //获取比赛ID
					$show_list[$v['link_id']] = $v;
				}else{
					$v['topic']['title'] = $topic_arr[$v['link_id']];
					$show_list["t_".$k] = $v;
				}// if($v['type']
                                
			} // foreach ($list end
			if($id_str != ''){
				$url = $this->config->item('api_5u').'/v3/v3_api/get_info/getWinMatchByInId/SDsFJO4dS3D4dF64SDF46?id_arr='.$id_str;
				$match_list = get_urldata($url);
				foreach ($match_list as $item) {
					$show_list[$item['id']]['match'] = $item;
				}
			} // if($id_str
			$arr_return['show_list'] = array_values($show_list); 
				if(!in_array($type ,array(4, 5, 6, 7))){  //非预约栏目
					$arr_week = array("日","一","二","三","四","五","六");
					$result = array();
					foreach ($show_list as $item) {
								$date = date("Y-m-d", $item["start_time"]);
								$result[$date]['show_list'][] = $item;
								$result[$date]['date'] = $date;
								$result[$date]['week'] = "星期".$arr_week[date("w", $item["start_time"])];
					}
					$arr_return["show_list"] = array_values($result); //重新排序
				}//if($type != 4)
			}// if(!empty($list))
        output($arr_return);
	}
	
	//通过ID刷新节目列表
	public function refreshshowbyidstr($token)
	{
		$token = urldecode($token);
		$idstr = trim($_GET['idstr']);
		if(empty($idstr)) output($this->res);
		$id_arr = explode(',',$idstr);
		$idstr = '';
		foreach ($id_arr as $id) {
			if(is_numeric($id)) $idstr .= ",".$id;
		}	
		$idstr = substr($idstr,1);
		$list = $this->base_model->select('show',"id in ($idstr)");
		$show_list = array();
		$result = array();
		if(!empty($list)){
			$topic_arr = $this->get_topic_type();
			$id_str = '';
			foreach ($list as $k=>$v) {
				if($v['end_time']<time()){  //节目状态
						$v['show_status'] = 0;// 0已结束 
				}else if($v['start_time']<=time() && $v['end_time']>=time()){
						$v['show_status'] = 1;// 1直播中 
				}else{
						if($token != 'NULL') $check_reserve = $this->liaoqiu_show_reserve_model->getRecordByshowIdUid($v['id'],$member_id);
						$v['show_status'] = !empty($check_reserve) ? 2 : 3;// 2已预约 3预约
				} 
				$uid_str = '';
				$uid_str .= $v['zhubo1'].",".$v['zhubo2'];  //一次性获取主播信息
				$zhuobo = $this->liaoqiu_member_model->getMemberByManyMemberID($uid_str);
				$v['zhubo'] = $zhuobo;
				if($v['type'] == 1){
					$id_str .= $v['link_id'].","; //获取比赛ID
					$show_list[$v['link_id']] = $v;
				}else{
					$v['topic']['title'] = $topic_arr[$v['link_id']];
					$show_list["t_".$k] = $v;
				}// if($v['type']
			} // foreach ($list end
			if($id_str != ''){
			$url = $this->config->item('api_5u').'/v3/v3_api/get_info/getWinMatchByInId/SDsFJO4dS3D4dF64SDF46?id_arr='.$id_str;
			$match_list = get_urldata($url);
			foreach ($match_list as $item) {
					$show_list[$item['id']]['match'] = $item;
				}
			} // if($id_str
		}//if(!empty($list))
		$this->res["show_list"] = array_values($show_list); //重新排序
		$this->res["state_code"] = 0;
        $this->res["state_desc"] = "刷新成功";
        output($this->res);
	}
	
	//通过ID修改节目封面
	public function editshowbanner($token,$id)
	{
		$token = urldecode($token);
        $id = trim($id);
		$url = urldecode(trim($_GET['url']));
		if(empty($token)||empty($id)||empty($url))  output($this->res);
		if(!is_numeric($id)) {
            $this->res["state_desc"] = "ID类型错误";
            output($this->res);
        }
		$member = getuser_by_token($token);
		if($member['role']!=2){
            $this->res["state_desc"] = "您没有权限！";
            output($this->res);			
		}
		$this->load->helper('huanxin');
		$table = 'show';
		$where = array('id'=>$id);
		$show = $this->base_model->row($table,$where);
		if(!$show){
			$this->res["state_desc"] = "该节目不存在！";
			output($this->res);	
		}
        $type=explode('.',$url);
		$type = ".".strtolower($type[count($type)-1]);
		$file_type = file_type($type);
		$true = $this->base_model->update($table,$where,array('banner_url'=>$url,'file_type'=>$file_type));
		if($true){
				$url = strstr($show['banner_url'],'/upload');
				@unlink(".".$url);				
				$params = array(
							"action"=>"editshowbanner",
							"to"=>$show['hx_room_id'],
							"type"=>"cmd",
							"target_type"=>"chatgroups",
							"ext"=>array("cmd"=>"cmd_room_top_change","url"=>$url,"file_type"=>$file_type),
				);
				$result = huanxin("sendshowmsg", $params);
				$this->res["state_code"] = 0;
       			$this->res["state_desc"] = "成功";				
		}else{
				$this->res["state_code"] = -1;
       			$this->res["state_desc"] = "失败";				
		}
        output($this->res);
	}
	
	//通过ID获取节目列表
	public function getshowbyid($id)
	{
		if(!is_num($id)) {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "ID类型错误";
            output($this->res);
        }
        $show = $this->liaoqiu_show_model->getAvalableRecordByID($id);
		$this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
        $this->res["show"] = $show;
        output($this->res);
	}
	

	//通过比赛ID获取节目列表
	public function getshowbymatchid($id)
	{
		if(!is_num($id)) {
            $this->res["state_desc"] = "ID类型错误";
            output($this->res);
        }
        $show = $this->liaoqiu_show_model->getRecordByMatchId($id);
		$this->res["state_code"] = 0;
        $this->res["state_desc"] = "成功";
		$this->res["show"] = $show;
        output($this->res);
	}
	


	//预约节目
    public function reserveshow($token, $show_id, $status) {
        $show_id = urldecode($show_id);
        $status = urldecode($status);
        $token = urldecode($token);

		//判断参数完整性
		if(!isset($token) || !isset($status) || !isset($show_id)) output($this->res);
        if($status != "1" && $status != "0") {
			$this->res["state_desc"] = "status错误";
			output($this->res);
		}
        $show = $this->liaoqiu_show_model->getNotStartRecordByID($show_id);
        if(empty($show)){
            $this->res["state_code"] = -4;
			$this->res["state_desc"] = "节目已经关闭或者正在直播中，不支持预约";
			output($this->res);
        }

		//校验token, 并顺便取用户基本信息
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];
		$params = array("show_id"=>$show_id,"member_id"=>$member_id);
		$table = "show_reserve";
		$reserve =  $this->base_model->row($table,$params);
		$params["status"]=$status;
		$params["time"]=time();
		$result = $reserve ? $this->base_model->update($table,array('rid'=>$reserve['rid']),$params) : $this->base_model->insert($table,$params);
        if($result){
        	$this->res["state_code"] = 0;
			$this->res["state_desc"] = "操作成功";
        } else {
            $this->res["state_desc"] = "服务器繁忙，请稍候重试！";
        }
        output($this->res);
    }

	 public function entershow($token,$show_id) {
	        $token = urldecode($token);
			$show_id = urldecode($show_id);
	        if(!isset($token) || !isset($show_id) || !is_numeric($show_id)) output($this->res);
	        //校验token, 并顺便取用户基本信息
	        $result = getuser_by_token($token);
	        $member_id = $result["member_id"];
			$data = array('member_id'=>$member_id,'show_id'=>$show_id);
			$this->load->model('liaoqiu_showlogs_model');
			$true = $this->liaoqiu_showlogs_model->getCountByshowIDMemberID($data);
			if($true == 0){
				$data['logs_time']=time();
				$true = $this->liaoqiu_showlogs_model->setRecord($data);
				$this->base_model->setinc('show',"id=$show_id","audience_num");
			}
			if($true){
	        	$this->res["state_code"] = 0;
				$this->res["state_desc"] = "操作成功";
	        } else {
	            $this->res["state_desc"] = "操作失败";
	        }
	        output($this->res);	
	 }
		
	//在线人数
	 public function showlogs($show_id,$last_id='NULL') {
			$show_id = urldecode($show_id);
			$last_id = urldecode($last_id);
	        if(!isset($last_id) || !isset($show_id) || !is_numeric($show_id)) output($this->res);
			$where = "show_id=$show_id";
			if($last_id!='NULL' && is_numeric($last_id))
				$where .= " and a.id > $last_id";
			$field = "a.id,a.member_id,member_logo,logs_time,account,nick_name,hx_username";
			$join = "JOIN liaoqiu_member as b ON b.member_id = a.member_id ";
			$showlogs = $this->base_model->select("showlogs",$where,$field,"logs_time",$join,50);

			$this->res["showlogs"] = $showlogs;
        	$this->res["state_code"] = 0;
			$this->res["state_desc"] = "操作成功";
	        output($this->res);	
	 }	
	//神评列表 
	 public function good_comment($token,$show_id) 
	 {
	 	$token = urldecode($token);
		$show_id = urldecode($show_id);		
				 
		if(!isset($show_id) || !is_numeric($show_id)) output($this->res);
		if($token != 'NULL'){
				$member = getuser_by_token($token);
				$member_id = $member["member_id"];
		}		 
		 $where = "b_id ='$show_id' and zan_num > 0";
		 $join = "JOIN liaoqiu_member ON liaoqiu_member.member_id = a.a_id ";
		 $list = $this->base_model->select('chatlogs',$where,'',"zan_num,desc",$join,100);
		 foreach ($list as $k=>$v) {
		 	unset($list[$k]['hx_password']);
			unset($list[$k]['token']);
			unset($list[$k]['token_time']);
			unset($list[$k]['add_time']);
		 	if(isset($member_id)){
			 	$count = $this->base_model->num('show_chatlogs_zan',array('member_id'=>$member_id,'hx_msgid'=>$v['hx_msgid']));
			 	$list[$k]['zan_status'] = $count?1:0; 		 		
		 	}else
				$list[$k]['zan_status'] = 0; //默认
			if($v['file_id']){
				$file = $this->base_model->row('file',array('id'=>$v['file_id']));
				if(!empty($file)){
					$list[$k]['file_content'] = $file;
				}
			}//if($file_id) 
		}//foreach
		 $this->res["list"] = $list;
	     $this->res["state_code"] = 0;
		 $this->res["state_desc"] = "神评列表";
		 output($this->res);	
	 }	 

  //获取节目菜单列表
   public function shownav() {
        $app_type = @urldecode($_REQUEST["app_type"]);
        //判断参数完整性
		if(!isset($app_type)) output($this->res);
		$menu = $this->my_memcached->get("shownav");
		if(empty($menu)){
	        $this->load->model('liaoqiu_league_name_model');
	        $menu = $this->liaoqiu_league_name_model->getRecord();//赛程导航		
	        $this->load->model('liaoqiu_topic_type_model');
	        $topic_type_arr = $this->liaoqiu_topic_type_model->getAllRecord();//话题导航		
	        foreach ($topic_type_arr as $item) {
	        	$item['type'] = 2;
	            $menu[] = $item;
	        }
			$this->my_memcached->set("shownav",$menu,3600*24*30);			
		}
		return $menu;
    }

    /**
     * 定制主播
     * 
     */
    private function get_customer_anchors($member_id)
    {
        $anchors = $list = $my_follows = array();
        load_model('friendship');
        //我关注的好友
        $follows = $this->liaoqiu_friendship_model->getRecordsByBID($member_id, array());

        if(!empty($follows))
        {
            foreach ($follows as $follow)
            {
                if($follow['status'] == Liaoqiu_friendship_model::STATUS_NORMAL)
                {
                    $my_follows[] = $follow['b_id'];
                }
            }
        }
        
        //我关注的主播
        if(!empty($my_follows))
        {
            $anchors = $this->liaoqiu_member_model->getMemberList(array('role' => Liaoqiu_member_model::ROLE_ANCHOR, 'members' => $my_follows));
        }


        if(!empty($anchors))
        {
            foreach ($anchors as $row)
            {
                $list[] = $row['member_id'];
            }
            $list = $this->liaoqiu_show_model->getRecordByZhubo($list);
        }

        unset($anchors, $my_follows);
        
        return $list;
    }
    
    /**
     * 定制球队
     * 
     */
    private function get_customer_team($member_id)
    {
        $this->load->helper('huanxin_helper');
        $list = $result_5u_api = array();
        $this->load->model('liaoqiu_focusteam_model');
        $focus_team_arr = $this->liaoqiu_focusteam_model->getByMemberID($member_id);

        if(!empty($focus_team_arr)  && !empty($focus_team_arr["focusteam"]))
        {
            $team_id = $focus_team_arr["focusteam"];
            $team_id = deal_width_symbol($team_id, ",");
            $params = array("team_id"=>$team_id);
            $result_5u_api = json_decode(usport_api("get_team_detail", $params), true);
        }

        //赛程ID
        $league_arr = array();
        if(!empty($result_5u_api) && ($result_5u_api['state_code'] == 0))
        {
            foreach ($result_5u_api['team_detail'] as $row)
            {
                if(isset($league_arr[$row['league_id']]))
                {
                    continue; 
                }
                $league_arr[$row['league_id']] = array($row['id'], $row['league_id']);
            }
        }

        //根据赛程获取节目
        if(!empty($league_arr))
        {
            $league_arr = array_values($league_arr);
            $list = $this->liaoqiu_show_model->getRecordByLinkID($league_arr, 1);
        }
        
        return $list;
    }
    
    /**
     * 回顾
     */
    private function get_member_history($member_id)
    {
        $list = $show_arr = array();
        load_model('showlogs');
        $showlogs = $this->liaoqiu_showlogs_model->getRecordByWhere(array('member_id' => $member_id));

        if(!empty($showlogs))
        {
            foreach ($showlogs as $row)
            {
                $show_arr[$row['show_id']] = $row['show_id'];
            }
            $show_arr = array_values($show_arr);
        }

        if(!empty($show_arr))
        {

            $list = $this->liaoqiu_show_model->getRecordByIds($show_arr);
        }
        return $list;
    }

//获取话题类型
	private function get_topic_type(){
				$topic_arr = $this->my_memcached->get("topic_type");
				if(empty($topic_type)){
					$topic_type = $this->base_model->select('topic_type',"status=1","id,title");
					foreach ($topic_type as $topic) {
						$topic_arr[$topic['id']] = $topic['title'];
					}
					$this->my_memcached->set("topic_type",$topic_arr,3600*24*30);					
				}
				return $topic_arr;
	}

}












/*	暂时不用
	//支持主客球队
   public function supportteam($member_id, $match_id, $team) {
        $match_id = urldecode($match_id);
        $team = urldecode($team);
		//判断参数完整性
		if(!isset($member_id) || !isset($team) || !isset($match_id)) output($this->res);

		if(!is_num($match_id)) {
			$this->res["state_desc"] = "match_id错误";
			output($this->res);
		}
        if($team != "1" && $team != "0") {
			$this->res["state_desc"] = "team错误";
			output($this->res);
		}

        $params = array("match_id"=>$match_id);
		//step2: 通过接口，获取赛程。
		$this->load->helper('huanxin');
		$result_5u_api = json_decode(usport_api("get_lq_match_list", $params), true);
        if(empty($result_5u_api["game_array"])){
        		$this->res["state_desc"] = "比赛已经结束，下次早点来哦";
				output($this->res);
        }else if($result_5u_api["game_array"][0]["games"][0]["game_state"]!="0" && $result_5u_api["game_array"][0]["games"][0]["game_state"]!="1"){
        		$this->res["state_desc"] = "比赛已经结束，下次早点来哦";
				output($this->res);
        }
		$this->load->model('liaoqiu_match_support_model');
        $params = array("match_id"=>$match_id,"support_team"=>$team,"member_id"=>$member_id,"time"=>time());
		$request_result = $this->liaoqiu_match_support_model->setRecord($params);
        if($request_result < 0){
			$this->res["state_desc"] = "支持主客队失败！";
			output($this->res);
        }
    }

    public function getmatchsupport($match_id) {
        $match_id = urldecode($match_id);
		//判断参数完整性
		if(!isset($match_id) || !is_num($match_id)) output($this->res);
		$this->load->model('liaoqiu_match_support_model');
		$request_result = $this->liaoqiu_match_support_model->getSumByMatchID($match_id);

        $support_data = array("host" => 0,"guest" => 0,);
        if(!empty($request_result)) foreach ($request_result as $v) {
            if($v["support_team"]=="0"){
                $support_data["guest"]++;
            } else {
                $support_data["host"]++;
            }
        }
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "操作成功";
        $this->res["support_data"] = $support_data;
        output($this->res);
    }	
*/
?>