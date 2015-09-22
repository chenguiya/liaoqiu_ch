<?php error_reporting(0); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域
class Robot extends CI_Controller {
	private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "非法提交",
    );
	public function __construct(){
		parent::__construct();
		$this->load->model('liaoqiu_show_model');
		$this->load->model('liaoqiu_showlogs_model');
		$this->load->model('liaoqiu_member_model');
		$this->load->helper('huanxin');
		$this->load->library('MY_Memcached');
        verify_sign();
	}
	public function nickname()
	{
		
		if($_GET['del']){
			$this->my_memcached->delete('nickname_num');
			echo "删除成功";die;
		}
		$num = $this->my_memcached->get("nickname_num");
		if(empty($num)){
			$num = 1;
			$this->my_memcached->set("nickname_num",$num,0,3600*5);
			if($num==1) $num = 0;
		}	
		$list = $this->base_model->select("member","a.status=1 and a.id>$num","","id,asc","JOIN liaoqiu_member_l ON liaoqiu_member_l.userid=a.member_id ",300);
		echo '<pre>';
		print_r($list);
		foreach ($list as $item) {
			$name = $item['nickname']?$item['nickname']:"球迷_".time();
			$this->base_model->update("member",array('id'=>$item['id']),array('nick_name'=>$name));
		}
		$num = $list[count($list)-1]['id'];
		$this->my_memcached->set("nickname_num",$num,0,3600*5);
		
	}
	public function test(){
		$redis = new redis();
		$redis->connect("203.195.240.33",6379);
		$x=$redis->get('foos');
				echo '<pre>';
		print_r($x);die;
		//$this->my_memcached->set('num',100,10);
		$x = $this->my_memcached->get('foos');
		echo '<pre>';
		print_r($x);
		echo $x."1";	
	}

	public function delfile(){
		$key = trim($_GET['key']);
		$path = trim($_GET['path']);
		if($key!="liuzy") die("签名不正确");
		$true = @unlink("./upload/".$path);
		if($true) echo "成功";
		else 
			echo "失败";
	}
	//创建聊天室 
	public function createroom(){
		$robot_list = $this->base_model->select("member","role=3","member_id");
		$rand = rand(0,count($robot_list)-1);
		$owner = $robot_list[$rand]['member_id'];			
		$params = array('name'=>'名称','description'=>'描述','maxusers'=>5000,'owner'=>'5usport_'.$owner);
		$this->load->helper('huanxin');
		$huanxin_result = huanxin('newroom', $params);
        if(!$huanxin_result["huanxin_result"]["data"]["id"]) {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试4";
            output($this->res);
        }
		$hx_roomsid = $huanxin_result["huanxin_result"]["data"]["id"];
		if($hx_roomsid){
			return array('room_id'=>1,'hx_roomsid'=>$hx_roomsid);
		}else{
			$this->res["state_desc"] = "创建房间失败";
			output($this->res); 
		}
		
	}

	//新增/修改节目
	public function _show_edit($id)
	{
		$hx_room_id = $this->createroom();
		$true = $this->base_model->update('show',array('id'=>$id),array('room_id'=>1,'hx_room_id'=>$hx_room_id['hx_roomsid']));
		if(!$true){
			$this->res["state_desc"] = "失败";
			output($this->res); 
		}
	}	

	//新增/修改节目
	public function show_edit()
	{
		$mem = new Memcache;
		$mem->connect("192.168.11.7",11212);
		if($_GET['del']){
			$mem->delete('lq_num');
			echo "删除成功";die;
		}
		$num = $mem->get("lq_num");
		echo $num;die;
		if(empty($num)){
			$num = 1;
			$mem->set("lq_num",$num,0,3600*5);
		}		
		$list = $this->base_model->select('show',"id > $num",'id',"","",30);
		foreach ($list as $item) {
			$this->_show_edit($item['id']);
		}
		$num = $list[count($list)-1]['id'];
		$mem->set("lq_num",$num,0,3600*5);
		echo "ok";
		
	}
	

	//写微博
	public function setweibo(){
		$url = "http://www.usport.com.cn/plugin.php?id=fansclub:api&ac=mytest";
		$data = file_get_contents($url);
		$list = json_decode($data,true);
		$m = $this->base_model->select("member","role=2");
		foreach ($list as $v) {
			if($v["authorid"] && $v['img']){
					$params = array(
						'file_path'=>$v['img'][0],
						'file_type'=>0,
						'member_id'=>$m[rand(0,count($m)-1)]['member_id'],
						'content'=>$v['message'],
						'w_time'=>time(),
				   );
				$this->base_model->insert("weibo_data",$params);				
			}
		}
		echo 'ok';
	}
	
	//设置头像
	public function setheader(){
		$url = "http://liaoqiu.usport.cc/upload/head/data/noavatar_middle.jpg";
		$table = 'member';
		$list = $this->base_model->select($table,'status=1','member_id');
		foreach ($list as $v) {
			if(!$v['member_logo']) $this->base_model->update($table,array('member_id'=>$v['member_id']),array('member_logo'=>$url));
		}
		echo 'ok';
	}

	
	//设置头像
	public function editheader(){
		$table = 'member';
		$list = $this->base_model->select($table,'status=1','member_id,member_logo');
		foreach ($list as $v) {
			if(!empty($v['member_logo'])&&!strstr($v['member_logo'],"http")){
				$url = "http://".$v['member_logo'];
				$this->base_model->update($table,array('member_id'=>$v['member_id']),array('member_logo'=>$url));				
			} 
		}
		echo 'ok';
	}


	//获取用户数据=>把头像写入数据库
    public function getmember()
	{
		$mem = new Memcache;
		$mem->connect("192.168.11.7",11212);
		if($_GET['del']){
			$mem->delete('num');
			echo "删除成功";die;
		}
		$mem->set("num",$num,0,3600*5);
		$num = $mem->get("num");
		echo $num;die;
		if(empty($num)){
			$num = 1;
			$mem->set("num",$num,0,3600*5);
		}
		$list = $this->base_model->select('member','status=1 and id >'.$num,'','id,asc','',50);
		foreach ($list as $key => $v) {
			if($v['member_logo']==""){
				$member_logo = passport_avatar_show($v['member_id'],$size = 'middle',true);
				$r = explode('?',$member_logo);
				$member_logo = $r[0];
				$dir = strstr($member_logo,'/000');
				$dir_arr=explode('/',$dir);
				unset($dir_arr[0]);
				$upload_path = './upload/head/data/avatar/';
				foreach ($dir_arr as $k=>$item) {
					   	if(!is_dir($upload_path)) mkdir($upload_path,0777);
						if($k!=count($dir_arr)) $upload_path = $upload_path.$item;
						if($k<count($dir_arr)-1) $upload_path .= "/";
				}
				$upload_path = strstr($upload_path, 'head');
				$img = getImg($member_logo,$upload_path);
				if($img=="0"){
					$img_url = "http://lq.5usport.com/upload/head/data/avatar/noavatar_middle.jpg";
				}else{
					$img_url = strstr($img, '/');
					$img_url = "http://lq.5usport.com".$img_url."?t=".time();
				}
				$this->base_model->update('member',array('member_id'=>$v['member_id']),array('member_logo'=>$img_url));
			}
		}
		$num = $list[count($list)-1]['id'];
		$mem->set("num",$num,0,3600*5);
		echo "ok";
		
	}


	//获取节目数据=>搬迁到2.0
    public function getgroup()
	{
		$list = $this->base_model->select('group_lzy','status=1','','id,asc');
		$id_str = '';
		$show_list = array();
		foreach ($list as $k => $v) {
				if($v['group_type'] == 1){
					$id_str .= $v['match_id'].","; //获取比赛ID
					$show_list[$v['match_id']] = $v;
				}else{
					$v['topic'] = $this->base_model->row('topic',array('id'=>$v['topic_id']));
					$show_list["t_".$k] = $v;
				}// if($v['type']			
		}
		if($id_str != ''){
			$url = $this->config->item('api_5u').'/v3/v3_api/get_info/getWinMatchByInId/SDsFJO4dS3D4dF64SDF46?id_arr='.$id_str;
			$match_list = get_urldata($url);
			foreach ($match_list as $item) {
				$show_list[$item['id']]['match'] = $item;
			}
		} // if($id_str
		$show_list = array_values($show_list); //重新排序
		foreach ($show_list as $k => $item) {
			if(isset($item['match']) || isset($item['topic'])){
						if($item['group_type'] == 1){  //比赛
							$link_id = $item['match_id'];
							$start_time=$item['match']['match_time'];
							$end_time= $item['match']['end_time'];
							$league_id = $item['match']['league_id'];
							$banner_url=$item['match']['a_logo'];
						}else{ //话题
							$link_id = $item['topic']['type'];
							$start_time=$item['topic']['start_time'];
							$end_time= $item['topic']['end_time'];
							$league_id = 0;
							$banner_url=$item['topic']['poster_url'];
						}
						$group_id = $item['id'];
						$title = $item['title'];
						$type=$item['group_type'];
						$zhubo1=$item['zhubo1'];
						$zhubo2=$item['zhubo2'];
						$add_time= $item['add_time'];
						$this->show_add($group_id,$title, $type, $link_id, $zhubo1, $zhubo2, $start_time, $end_time, $add_time, $banner_url, $league_id);			
			}
		}
		echo "ok";
	}

	public function getshowchatlogs(){
		$mem = new Memcache;
		$mem->connect("192.168.11.7",11212);
		if($_GET['del']){
			$mem->delete('lq_chatlog');
			echo "删除成功";die;
		}
		$list = $mem->get("lq_chatlog");
		if(empty($list)){
			$_list = $this->base_model->select("show_lzy","start_time >= 1438963200");
			$mem->set("lq_chatlog",$_list,0,3600*5);
		}
		foreach ($list as $k => $v) {
			$bid = $v['group_id'];
			$show_id = $v['id'];
			$chatlogs_list = $this->base_model->select("chatlogs_true","b_id=$bid");
			foreach ($chatlogs_list as $item) {
				$a_id = $item['a_id'];
				$b_id = $show_id;
				$file_id = $item['file_id'];
				$hx_msgid = $item['hx_msgid'];
				$type = $item['type'];
				$content_type = $item['content_type'];
				$content = $item['content'];
				$chattime = $item['chattime'];
				$zan_num = $item['zan_num'];
				$this->chatlogs_add($a_id,$b_id,$file_id,$hx_msgid,$type,$content_type,$content,$chattime,$zan_num);
			}
			unset($list[$k]);
			$mem->set("lq_chatlog",$list,0,3600*5);
			die("ok");
		}
		echo '<pre>';
		print_r($list);
	}

	//新增聊天记录
	public function chatlogs_add($a_id,$b_id,$file_id,$hx_msgid,$type,$content_type,$content,$chattime,$zan_num)
	{
			$params = array(
							'a_id'=>$a_id,
							'b_id'=>$b_id,
							'file_id'=>$file_id,
							'hx_msgid'=>$hx_msgid,
							'type'=>$type,
							'content_type'=>$content_type,
							'content'=>$content,
							'chattime'=>$chattime,
							'zan_num'=>$zan_num,
					   );
			$result = $this->base_model->insert("chatlogs",$params);
			if(isset($result) && !empty($result)){
				$arr_return = array('state_code'=>0,'state_desc'=>'创建成功','id'=>$result);
			}else{
				$arr_return = array('state_code'=>-99,'state_desc'=>'创建失败','id'=>0);
				//echo '<pre>';
				//print_r($params);
				//output($arr_return); 		
			}
			//output($arr_return); 			

	}

	//新增/修改节目
	public function show_add($group_id,$title,$type,$link_id,$zhubo1,$zhubo2,$start_time,$end_time,$add_time,$banner_url,$league_id)
	{
			$params = array(
							'group_id'=>$group_id,
							'hx_room_id'=>'104006880111624712',
							'title'=>$title,
							'desc'=>$title,
							'type'=>$type,
							'link_id'=>$link_id,
							'zhubo1'=>$zhubo1,
							'zhubo2'=>$zhubo2,
							'start_time'=>$start_time,
							'end_time'=>$end_time,
							'banner_url'=>$banner_url,
							'file_type'=>0,
							'status'=>1,
							'add_time' => $add_time,
					   );
			if($type == 1) $params['league_id']=$league_id;
			$result = $this->base_model->insert("show",$params);
			if(isset($result) && !empty($result)){
				$arr_return = array('state_code'=>0,'state_desc'=>'创建成功','id'=>$result);
			}else{
				$arr_return = array('state_code'=>-99,'state_desc'=>'创建失败','id'=>0);
				//echo '<pre>';
				//print_r($params);
				//output($arr_return); 		
			}
			//output($arr_return); 			

	}
	



	public function ajax_return_showlogs(){
		$robot_arr = array();
		if(is_num($_GET['show_id'])){
			$showlogs = $this->liaoqiu_showlogs_model->getRecordsByshowID($_GET['show_id']); //获取加群用户
			foreach ($showlogs as $key => $item) {
				$member = $this->liaoqiu_member_model->getMemberByMemberID($item['member_id']);   //通过聊球表筛选机器人
				if($member['role'] == 3) $robot_arr[] = $member;
			}//foreach
		}
		echo json_encode($robot_arr);
	}
		
	public function edit_nickname()
	{
		include_once("words.php");
		$name_num = count($robot_config['name'][1]);
		if($this->memcache->get('lq_num')){
			$num = $this->memcache->get('lq_num');
			$this->memcache->set('lq_num',$num+1,3600); 
		}else{
			$this->memcache->set('lq_num',1,3600);
			$num = $this->memcache->get('lq_num');
		}
		$num = $num-1;
		if($num > $name_num) die('全部更改完成');
		$robot_arr = $this->memcache->get('lq_robot_arr');
		if(empty($robot_arr)){
			$member_list = $this->getRobotMember();
			$robot_arr = array();
			foreach ($member_list as $key => $v) {
				$robot_arr[] = $v;
				if($key > $name_num) break;
			}
			$this->memcache->set('lq_robot_arr',$robot_arr,3600); 
		}		
		$nickname_arr = array();
		$userid = $robot_arr[$num]['member_id'];
		$name =   $robot_config['name'][1][$num];
		$data = array(
			'nickname' => $name,
			'username' => $name,
		);
		$this->member_model->editMember($userid,$data);
		$data = array(
			'account' => $name,
		);
		$this->liaoqiu_member_model->editMemberByMemberID($userid,$data);
		echo 'ok';
	}	
	
	//自动注册
    public function register() {
    	die('暂不允许注册！');
    	include_once("words.php");
		$emailarr = array('163','139','sina','126');
		$randnum = rand(1000000000,99999999999);
		$str = chr(rand(97, 122)).chr(rand(97, 122));
		$email = $str.$randnum."@".$emailarr[rand(0,3)].".com";
        $password = '5usportliuzy';
		$sex = rand(1,2);
        //判断参数完整性
        $email_patern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        $nickname_patern = "/[a-zA-Z0-9\u4e00-\u9fa5]{2,20}/";

        if(isset($email) && isset($password))
        {
            if(!preg_match($email_patern, $email)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "邮箱格式错误";
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
        $params["password"] = $password;
		
		$nickname = $robot_config['name'][$sex][rand(0,(count($robot_config['name'][$sex])-1))].chr(rand(97,122)); 
        //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
        $result_5u_api = json_decode(usport_api("register_email_1", $params),true);
		if($result_5u_api['state_code']==0){
			$this->register_step2($result_5u_api['token'], $nickname);
		}
        output($result_5u_api);
    }

 	//加入聊天室
	public function entershow($show_id,$num=10){
		if(is_numeric($_GET['id'])) $show_id = trim($_GET['id']);
		if(is_numeric($_GET['num'])) $num = trim($_GET['num']);
		if(!is_numeric($show_id)) {
            $this->res["state_desc"] = "id错误";
            output($this->res);
        }
		
		$member_list = $this->getRobotMember();
		$count = count($member_list);
		if($num > $count) $num = $count;
		$member_arr = array();
		for ($i=0; $i < $num; $i++) {
			$key = rand(0,$count-1);
			if(!$member_arr[$key]) $member_arr[$key] = $member_list[$key];
										else  $i--;
		}
		unset($member_list);
       	$show_detail =$this->liaoqiu_show_model->getAvalableRecordByID($show_id);
		if($member_arr) foreach ($member_arr as $item) {
			$this->_reservematch($item['member_id'], $show_id);  //预约
			$this->_entershow($show_id,$item['member_id']);    //加入聊天室
		}
		echo "加人成功！";
	}
	
	//根据聊天室ID发言
	//   /robot/sendmsgbyshowid?sign=1&id=56&member_id=19426&msg=来吧
	public function sendmsgbyshowid(){
		$show_id = trim($_GET['id']);
		if(!is_numeric($show_id) || !is_numeric($_GET['member_id'])) die('参数有误！');
		$show = $this->liaoqiu_show_model->getAvalableRecordByID($show_id);
		if(!empty($show)) $this->_sendmsg($show);
		echo 'ok';		
	}
	
	//加入聊天室并随机聊天
	public function getshowandsendmsg(){
			include_once("words.php");		
			$show_list = $this->liaoqiu_show_model->getAllStartRecord();
			if(!empty($show_list)){
				foreach ($show_list as $k => $item) {
					 $this->_sendmsg($item,$robot_config);
				}//for
			}else{
				 die('暂无直播节目');
			}	
		echo 'ok';		
	}

	
	//设置头像
    public function modify_head() {
		if (!isset($_COOKIE['touxiang'])){
			setcookie("touxiang",1,time()+3600);
			die('生成cookie');
		}else{
			setcookie("touxiang",$_COOKIE['touxiang']+5,time()+3600);
		}
		
		$robot_member = $this->getRobotMember();
		foreach ($robot_member as $key => $v) {
			if($key > $_COOKIE['touxiang']-1){  
				//读取图片文件，转换成base64编码格式
				$image_url = '/data/www/5usport/avatar/data/robot_head/';
				$head_arr = getfile($image_url);
				$head = count($head_arr)-1;
				$num = ($key > count($head_arr)) ? rand(0,$head) : $key;
				$sender_icon = $head_arr[$num];
				$image_file = $image_url.$sender_icon;
				$image_info = getimagesize($image_file);
		        $arr_return = array();
		        if($image_info == '')
		        {
		        	die('有误');
		            $input = 'data:image/gif;base64,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 MbGLXeQ8H/vVoJVwN5tcZk3b+tbRlrYmAgEAOw==';
		        }
		        else
		        {
		            $input = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($image_file)));
		        }
		        $result = passport_avatar_create($v['member_id'], $input);
				
				if($result === TRUE)
		        {
		            $logo_url = passport_avatar_show($v['member_id'], 'middle', TRUE);
		            $this->res["state_code"] = 0;
		            $this->res["state_desc"] = "操作成功";
		            $this->res["member_logo"] = $logo_url;
		        }
		        else
		        {
		            $this->res["state_code"] = -4;
		            $this->res["state_desc"] = '操作失败|'.$result;
		        }
		        print_r($this->res);
				if($key == $_COOKIE['touxiang']-1) die('ok');								
			}// IF if($key > $_COOKIE['touxiang'])
							
		}// FOR
		echo '头像添加成功！';
    }

	//发消息函数
	private function _sendmsg($show,$robot_config=array()){
		isEmpty($show);
		$top = 300; //默认最多加多少人
		$default_num = 20;
		$one = 	ceil($top/20);	 //默认每次加多少人
		if($one > $default_num) $one = $default_num;
		$show_id = $show['id'];
		$hx_room_id = $show['hx_room_id'];
		if(empty($_GET['member_id'])){
			$showlogs = $this->liaoqiu_showlogs_model->getRecordsByShowId($show_id); //获取加群用户
		}else{
			$check_member = $this->liaoqiu_showlogs_model->getRecordsByshowIDMemberID($show_id,$_GET['member_id']);
			if(empty($check_member)) $this->_entershow($show_id,$_GET['member_id']);    //加入聊天室
		}
		if(count($showlogs) > $one || !empty($_GET['member_id'])){  //少于30人说明没有机器人在里面
			 			if(empty($_GET['member_id'])){
			 				foreach ($showlogs as $key => $item) {
								$member = $this->liaoqiu_member_model->getMemberByMemberID($item['member_id']);
								if($member['role'] == 3) $robot_arr[] = $member;
							}//foreach
							$rand = rand(0,count($robot_arr)-1);
							$from = $robot_arr[$rand];
			 			}else{
			 				$member_id = trim($_GET['member_id']);
							$from = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
			 			}// if(empty($_GET['member_id']))
						
						$topic = $_GET['topic'] ? "#".$_GET['topic']."#" : "";
						if($_GET['msg']){
							$msg = $topic.trim($_GET['msg']);
						}else if($_GET['img']){
							$img = trim($_GET['img']);
						}else{
							$num = rand(0,count($robot_config['face'])-1); 
							$msg_str = $robot_config['face'][$num];  //暂时只发表情
							$msg = 	$topic.$msg_str; 
						}//IF if(empty($_GET['msg']))
						//$sender_icon = passport_avatar_show($from['member_id'],$size = 'middle',true);  //获取用户头像
						$params = array('from'=>'5usport_'.$from['member_id'],'to'=>$hx_room_id,'target_type'=>'chatgroups','sender_nickname'=>$from['account'],"sender_icon"=>$from['member_logo']);
						//print_r($params);
						if($params['from'] == '' || $params['to'] == ''){
							print_r($params);
							die('格式有误！');
						}
						if(isset($msg)){
							$params['message'] = $msg;
							$return = huanxin('sendlqtxtmsg',$params);
						}else if(isset($img)){
							$params['url'] = urldecode(trim($_GET['url']));
							$params['secret'] = $img;
							$return = huanxin('sendimgmsg',$params);
						}
						//print_r($return); 	
		
		}else{
			$this->entershow($show_id,$one);  //如果没有用户则自动加人
		}// if(count($showlogs) > $one ||
	} 


	//比赛预约预约函数
   private function _reservematch($member_id, $show_id) {
		$params = array("show_id"=>$show_id,"status"=>1,"member_id"=>$member_id,"time"=>time());
		$this->load->model('liaoqiu_show_reserve_model');
		$reserve = $this->liaoqiu_show_reserve_model->getRecordByshowIdUid($show_id,$member_id);
		if(empty($reserve)) $this->liaoqiu_show_reserve_model->setRecord($params);
    }

    //加入聊天室操作函数
 	 public function _entershow($show_id, $member_id) {
	        if(!isset($member_id) || !isset($show_id) || !is_numeric($show_id)) output($this->res);
			$data = array('member_id'=>$member_id,'show_id'=>$show_id);
			$true = $this->liaoqiu_showlogs_model->getCountByshowIDMemberID($data);
			if($true == 0){
				$data['logs_time']=time();
				$true = $this->liaoqiu_showlogs_model->setRecord($data);
				$this->base_model->setinc('show',"id=$show_id","audience_num");
			}			
	 }

	public function getRobotMember(){
		$robot_list = $this->my_memcached->get('robot_list');
		if(empty($robot_list)){
			$robot_list = $this->liaoqiu_member_model->getMemberByRole(3);
			$this->my_memcached->set('robot_list',$robot_list,3600*24*30);  //缓存一个月
		}
		return $robot_list;
	}
	
	private function _supportteam($member_id, $match_id) {
		$this->load->model('liaoqiu_match_support_model');
        $params = array();
        $params["match_id"] = $match_id;
        $params["support_team"] = rand(0,1);
        $params["member_id"] = $member_id;
        $params["time"] = time();
		$request_result = $this->liaoqiu_match_support_model->setRecord($params);
    }
	

    //会员注册第二步
    public function register_step2($token, $nickname) {
        if(isset($nickname) && isset($token))
        {
            if(!is_username($nickname)) {
                $this->res["state_code"] = -99;
                $this->res["state_desc"] = "昵称格式错误，2-20位字符，支持汉字、数字、字母";
                output($this->res);
            }
        } else {
            $this->res["state_code"] = -99;
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        $params = array();
        $params["nickname"] = $nickname;
        $params["token"] = $token;
        //step2: 通过接口，让5u先注册插入一条记录，因为要拿到userid，组成用户名发给环信。
        $result_5u_api = json_decode(usport_api("register_step_2", $params), true);
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
        $liaoqiu_result = array();

        $lq_reg_params = array();
        $lq_reg_params["member_id"] = $usport_userid;
        $lq_reg_params["account"] = $username;
        $lq_reg_params["hx_uuid"] = $hx_uuid;
        $lq_reg_params["hx_username"] = $hx_username;
        $lq_reg_params["hx_password"] = $hx_userpass;
        $lq_reg_params["role"] = "3";
        $lq_reg_params["token"] = $token;
        $lq_reg_params["token_status"] = "1";
        $lq_reg_params["token_time"] = $time;
        $lq_reg_params["add_time"] = $time;
        $lq_reg_params["pushnews"] = "1";
        $lq_reg_params["status"] = "1";

        $liaoqiu_result = $this->liaoqiu_member_model->setMember($lq_reg_params);
        if($liaoqiu_result == -2) {
            $this->res["state_code"] = -2;
            $this->res["state_desc"] = "用户已存在";
            output($this->res);
        } else {
            unset($result_5u_api["state_code"]);
            unset($result_5u_api["state_desc"]);
            unset($result_5u_api["username"]);
            unset($result_5u_api["userid"]);
            $lq_reg_params = array_merge($lq_reg_params, $result_5u_api);
        }

    }
    
}	
?>	