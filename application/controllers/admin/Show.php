<?php 

include_once("Base.php");
class Show extends Base {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "参数有误",
    );
	
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
		$this->load->model('liaoqiu_show_model');
		$this->load->model('liaoqiu_showlogs_model');
		$this->load->model('liaoqiu_member_model');
		$this->load->model('liaoqiu_topic_type_model');
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
			return $hx_roomsid;
		}else{
			$this->res["state_desc"] = "创建房间失败";
			output($this->res); 
		}
		
	}


	//新增/修改节目
	public function show_add()
	{
		if(!empty($_POST)){  //新增信息
			$id = urldecode($_POST['id']);
	        $title = urldecode($_POST['title']);
			$start_time = urldecode($_POST['start_time']);
			$end_time = urldecode($_POST['end_time']);
			$banner_url = urldecode($_POST['banner_url']);
			$type = urldecode($_POST['type']);
			$link_id = urldecode($_POST['link_id']);
			$zhubo1 = urldecode($_POST['zhubo1']);
			$zhubo2 = urldecode($_POST['zhubo2']);
            $changkong1 = urldecode($_POST['changkong1']);
            $changkong2 = urldecode($_POST['changkong2']);
            $changkong1 = !empty($changkong1) ? $changkong1 : 0;
            $changkong2 = !empty($changkong2) ? $changkong2 : 0;
			$file_type = urldecode($_POST['file_type']);
			$league_id = urldecode($_POST['league_id']);
			$status = urldecode($_POST['status']);
			$premier_url = urldecode($_POST['premier_url']);
			$premier_start_time = urldecode($_POST['premier_start_time']);
			$premier_end_time = urldecode($_POST['premier_end_time']);
			//判断参数完整性
	        if(!isset($type) || !isset($title) || !isset($zhubo1) || !isset($banner_url)) {
	            $this->res["state_desc"] = "参数错误";
	            output($this->res);
	        }
					
	        if(countstring($title)< 2 || countstring($title) > 50) {
	            $this->res["state_desc"] = "标题字数不规范，当前字数为：".countstring($title);
	            output($this->res);
	        }
			
			$result = $this->liaoqiu_show_model->getAllAvalableRecordInOneMonth();  //获取一个月内的节目表
			foreach ($result as $item) {
				if($id != $item['id'] ){  //不与本来节目进行比较
					if( (  //检查主播一档期
							($start_time >= $item['start_time'] && $start_time <= $item['end_time'])
						 || ($end_time >= $item['start_time'] && $end_time <= $item['end_time'])
						 || ($start_time >= $item['start_time'] && $end_time <= $item['end_time'])
						 )
						 && in_array($zhubo1,array($item['zhubo1'],$item['zhubo2']))
					  ){
					  	$this->res["state_desc"] = "主播一档期已满!";
						output($this->res); 
					}else if($zhubo2 != NULL &&  //检查主播二档期
							(
								($start_time >= $item['start_time'] && $start_time <= $item['end_time'])
							 || ($end_time >= $item['start_time'] && $end_time <= $item['end_time'])
							 || ($start_time >= $item['start_time'] && $end_time <= $item['end_time'])
							 )
							 && in_array($zhubo2,array($item['zhubo1'],$item['zhubo2']))){
						$this->res["state_desc"] = "主播二档期已满!";
						output($this->res); 
					}// if( ( END				
				}// if($_GET['id'] END	 
			}// foreach END
			
			$params = array(
							'title'=>$title,
							'desc'=>$title,
							'type'=>$type,
							'link_id'=>$link_id,
							'zhubo1'=>$zhubo1,
							'zhubo2'=>$zhubo2,
                            'changkong1' => $changkong1,
                            'changkong2' => $changkong2,
							'start_time'=>$start_time,
							'end_time'=>$end_time,
							'banner_url'=>$banner_url,
							'file_type'=>$file_type,
							'status'=>$status,
							'premier_url' => $premier_url,
							'premier_start_time' => $premier_start_time,
							'premier_end_time' => $premier_end_time,
							'add_time' => time(),
					   );
			if($type == 1) $params['league_id']=$league_id;		   
			if($id != 0){  //编辑
				if(!is_numeric($id)){
					$arr_return = array('state_code'=>-99,'state_desc'=>'参数有误！');
					output($arr_return); 
				}
				$row = $this->base_model->row('show',array('id'=>$id));
				$result = $this->liaoqiu_show_model->editRecordByID($id,$params);
				if($result){
					if($row['banner_url']!=$banner_url){
							$url = strstr($row['banner_url'],'/upload');
							@unlink(".".$url);
					}
				}
			}else{
				$hx_room_id = $this->createroom();  //创建房间
				$params['hx_room_id']=$hx_room_id;
				$result = $this->liaoqiu_show_model->setRecord($params);
			}		   
			if($result){
				$arr_return = array('state_code'=>0,'state_desc'=>'创建成功','id'=>$result);
				if($type == 1){
					//$url = $this->config->item('api_5u').'/v3/v3_api/get_info/editMatchInLiaoqiu/SDsFJO4dS3D4dF64SDF46?status=1&id='.$link_id;
					//file_get_contents($url); //修改为在聊球显示
				}
			}else{
				$arr_return = array('state_code'=>-99,'state_desc'=>'创建失败','id'=>0);
			}
			output($arr_return); 			
		}else{
			if(isset($_GET['id']) && is_numeric($_GET['id'])){  //修改信息
				$id = $_GET['id'];
				$show = $this->liaoqiu_show_model->getAvalableRecordByID($id);
				//echo '<pre>';
		//print_r($show);die;
				$this->data['show'] = $show;
			}// if
			$topic_type = $this->liaoqiu_topic_type_model->getAllRecord(); //获取所有话题类型
			$zhubo_list = $this->liaoqiu_member_model->getMemberByRole(2); //获取所有女主播
                        $changkong_list = $this->liaoqiu_member_model->getMemberByRole(4); //获取所有场控
                        
			$this->data['topic_type'] = $topic_type;
			$this->data['zhubo_list'] = $zhubo_list;
			$this->data['changkong_list'] = $changkong_list;
                        $this->show('show_add',$this->data);			
		} //if(!isset($_POST))

	}
	
	//节目列表
	public function showlist($now_page=1){
		if(isset($_GET['title'])){
			$title = trim($_GET['title']);
			if(is_numeric($title)){  //通过id查询
				$where =  "id=".$title;	
			}else{
				$where =  "title LIKE '%".$title."%'";	
			}
		}
		$count = $this->base_model->_count("show",@$where); //取数量
		$url = 'admin/show/showlist/';
		$page = $this->page_config($count,$now_page,$url,20); 
		$list = $this->liaoqiu_show_model->page_list($page['list_page'],$now_page,@$where);  
		if(!empty($list)){
			$topic_arr = $this->get_topic_type();  //话题类型
			$match_arr = $this->get_match_type();	//赛程类型
			$id_str = '';
			foreach ($list as $k=>$v) {
				$uid_str = '';
				$uid_str .= $v['zhubo1'].",".$v['zhubo2'];
				$zhuobo = $this->liaoqiu_member_model->getMemberByManyMemberID($uid_str);
				$v['zhubo'] = $zhuobo;
				if($v['type'] == 1){
					$v['show_type'] = $match_arr[$v['league_id']];
				}else{
					$v['show_type'] = $topic_arr[$v['link_id']];
					
				}
				$list[$k] = $v;
			}		
		}// if(!empty($list))
		$this->data['show_list'] = $list;
		$this->data['page'] =  $page['page'];
		$this->show('show_list',$this->data);
	}

	
	//赛程列表
	public function matchlist($now_page=1){
		$url = $this->config->item('api_5u').'/v3/v3_api/get_info/getSCMatchCount/SDsFJO4dS3D4dF64SDF46';
		$count = get_urldata($url); //取数量
		$add = 'admin/show/matchlist/';
		$page = $this->page_config($count,$now_page,$add);
		$url = $this->config->item('api_5u').'/v3/v3_api/get_info/getSCMatchPage/SDsFJO4dS3D4dF64SDF46?per_page='.$page['list_page'].'&p='.$now_page;
		$match_list = get_urldata($url); //取数量
		$topic_type = $this->liaoqiu_topic_type_model->getAllRecord(); //获取所有话题类型
		$zhubo_list = $this->liaoqiu_member_model->getMemberByRole(2); //获取所有女主播
		$this->data['match_list'] = $match_list;
		$this->data['topic_type'] = $topic_type;
		$this->data['zhubo_list'] = $zhubo_list;
		$this->data['page'] = $page['page'];	
		$this->show('match_list',$this->data);
	}
	
	//删除节目
	public function delshowbyid($id)
	{
		if(!is_numeric($id)) alert('参数错误','',true);
		$result = $this->liaoqiu_show_model->del_show_by_id($id);
		$result ? alert('删除成功','',true) : alert('删除失败','',true);
	}
	
	//真实用户数
	public function true_showlogs($now_page=1){
		$id = trim($_GET['id']);
		if(!is_numeric($id)) alert('group_id有误');
		$url = 'admin/show/true_showlogs/';
		$table = "showlogs";
		$count = $this->base_model->num($table,array("show_id"=>$id)); //取数量
		$page = $this->page_config($count,$now_page,$url,2);
		$showlogs = $this->base_model->page($page['list_page'],$now_page,$table,"show_id=$id","","logs_time,desc"); //获取加群用户
		$member_arr = array();
		foreach ($showlogs as $key => $item) {
					$member = $this->liaoqiu_member_model->getMemberByMemberID($item['member_id']);   //通过聊球表筛选机器人
					if($member['role'] != 1) $member_arr[] = $member;
		}//foreach
		$this->data['member_arr'] = $member_arr;
		$this->data['true_count'] = count($member_arr);
		$this->data['all_count'] = $count;
		$this->data['page'] = $page['page'];
		$this->show('true_showlogs',$this->data);			
	}			

	//马甲发言
	public function robot_talk($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$this->data['id']=trim($id);
		$this->show('robot_talk',$this->data);			
	}

	//增加马甲
	public function robot_add($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$this->data['id']=trim($id);
		$this->show('robot_add',$this->data);			
	}

	//聊天记录
	public function show_chatlogs($now_page=1){
		$id = trim($_GET['id']);
		if(!is_numeric($id)) alert('参数错误','',true);
		$table = "chatlogs";
		$join = 'JOIN liaoqiu_member ON liaoqiu_member.member_id = a.a_id ';
		$where = 'b_id='.$id;
		$order = 'chattime,desc';
		if(isset($_GET['good'])){  //神评
			$order = 'zan_num,desc';
			$where = $where.' and zan_num>0';
		}
		if(isset($_GET['content'])){  //神评
			$content = urldecode($_GET['content']);
			$where = $where." and content LIKE '%".$content."%'";	
		}
		$count = $this->base_model->_count($table,@$where); //取数量
		$url = "admin/show/show_chatlogs/";
		$page = $this->page_config($count, $now_page,$url,4);
		$list = $this->base_model->page($page['list_page'],$now_page,$table,$where,$join,$order);
		foreach ($list as $k=>$v) {
			if($v['file_id']){
				$file = $this->base_model->row('file',array('id'=>$v['file_id']));
				if(!empty($file)){
					$list[$k]['url'] = $file['5u_url'] ? $file['5u_url'] : $file['hx_url'];
				}
			}//if($file_id) 
		}//foreach
		$this->data['list'] = $list;
		$this->data['page'] = $page['page'];
		$this->show('show_chatlogs',$this->data);			
	}
	
	//增加点赞数
	public function zan_edit($id,$num){
		if(!is_numeric($id) && !is_numeric($num)) alert('参数错误','',true);
		$id = trim($id);
		$num = trim($num);
		$true = $this->base_model->update('chatlogs',array("hx_msgid"=>$id),array("zan_num"=>$num));
		echo $true ? 1 : -1;
	}	
			
	//删除记录
	public function del_chatlogs($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$id = trim($id);
		$true = $this->base_model->delete('chatlogs',array('hx_msgid'=>$id));
		$true ? alert('操作成功','',true) : alert('操作失败','',true);		
	}
			
	//话题类型列表
	public function show_type(){
		$table = isset($_GET['type'])?'league_name':'topic_type';
		$list = $this->base_model->select($table);
		$this->data['list'] = $list;
		$this->show('show_type',$this->data);	
	}	
			
	//话题类型修改
	public function type_eidt($id,$status){
		if(!is_numeric($id) && !in_array($status,array(0,1))) alert('参数错误','',true);
		$id = trim($id);
		$status = trim($status);
		$true = $this->base_model->update('topic_type',array('id'=>$id),array('status'=>$status));
		$type = isset($_GET['type'])?'?type=1':'';
		$true ? alert('操作成功','show/show_type'.$type) : alert('操作失败','',true);		
	}		
			
	//话题类型删除
	public function type_del($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$id = trim($id);
		$table = isset($_GET['type'])?'league_name':'topic_type';
		$true = $this->base_model->delete($table,array('id'=>$id));
		$true ? alert('操作成功','',true) : alert('操作失败','',true);		
	}
			
	//话题类型增加/编辑
	public function type_add(){
		if(!empty($_POST)){
			$id = urldecode($_POST['id']);
	        $title = urldecode($_POST['title']);
			$sort = urldecode($_POST['sort']);
			if(!empty($_FILES['file']['tmp_name'])){
				$file = upload_file('showico');
				$logo = $file['file_url'];
			}else{
				$logo = urldecode($_POST['logo']);
			}
			$data = array(
					'title' => $title,
					'logo' => $logo,
					'sort' => $sort,
					);
			$table = !empty($_POST['type'])?'league_name':'topic_type';	
			if($id != 0){  //编辑
				if(!is_numeric($id)){
					alert('id参数有误','',true);
				}//
				$true = $this->base_model->update($table,array('id'=>$id),$data);
			}else{
				$id = $true = $this->base_model->insert($table,$data);
			}//if($id != 0)
			$str = !empty($_POST['type'])?'&type=1':'';	
			$true ? alert('操作成功','show/type_add?id='.$id.$str) : alert('操作失败','',true);				
		}else{
			$table = isset($_GET['type'])?'league_name':'topic_type';	
			if(isset($_GET['id']) && is_numeric($_GET['id'])){  //修改信息
				$id = trim($_GET['id']);
				$row = $this->base_model->row($table,array('id'=>$id));
				$this->data['row'] = $row;
			}// if
			$this->show('show_type_add',$this->data);
		}//if($_POST)
	}	
		
			
	//上传节目封面接口
	public function upload_file()
	{
			$data = upload_file('show');
		    output($data);
	}

	//上传文件到环信	
	public function uploadimg()
	{
		$this->load->helper('huanxin');
		$data = array('file'=>file_get_contents($_FILES['file']['tmp_name']));
		$result = huanxin('uploadfile',$data);
		$return = array('code'=>-1,'desc'=>'操作失败');
		if(isset($result['huanxin_result']['entities'][0]['share-secret'])){
			$return = array(
				'code'=>'0',
				'desc'=>'操作成功',
				'filename'=>$_FILES['file']['name'],
				'secret'=>$result['huanxin_result']['entities'][0]['share-secret'],
				'url'=>$result['huanxin_result']['uri']."/".$result['huanxin_result']['entities'][0]['uuid']
			);
		}
		output($return);
	}
	
//获取话题类型
	private function get_topic_type(){
				$topic_type = $this->base_model->select('topic_type',"status=1","id,title");
				foreach ($topic_type as $topic) {
					$topic_arr[$topic['id']] = $topic['title'];
				}
				return $topic_arr;
	}	
	
//获取赛程类型
	private function get_match_type(){
				$match_type = $this->base_model->select('league_name',"status=1","id,title,league_id");
				foreach ($match_type as $match) {
					$match_arr[$match['league_id']] = $match['title'];
				}
				return $match_arr;
	}	
}
?>