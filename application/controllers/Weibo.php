<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Weibo extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "参数错误",
    );

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
       	verify_sign();
       	$this->load->model('liaoqiu_weibo_data_model');
		$this->load->model('liaoqiu_weibo_comment_model');
    }

	//创建微博
	//  weibo/createweibo/用户token/内容  //转发微博不做
	public function createweibo($token,$content,$weibo_id=NULL){
                $token = urldecode($token);
                $content = urldecode($content);
                        //判断参数完整性
                if(!isset($token) || !isset($content))  output($this->res);
                $data = array();		
                if(!empty($_FILES))
                {
                                $file = upload_file('weibo');
                                $data['file_path']=$file['file_url'];
                                $data['file_type']=$file['file_type'];
                }
                
                
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];
			
		$data['member_id']=$member_id;
		$data['content']=$content;
		$data['w_time']=time();
		$data['ip']=ip();
		if($weibo_id != 'NULL' && is_numeric($weibo_id)){
			$result =  $this->liaoqiu_weibo_data_model->editRecordByID($weibo_id,$data);		
		}else{
			$result =  $this->liaoqiu_weibo_data_model->setRecord($data);	
		}
        	
		$arr_return = $result ? array('state_code'=>0,'state_desc'=>'发布成功','id'=>$result) : array('state_code'=>-1,'state_desc'=>'发布失败');
		output($arr_return); 
	}
	
	//删除微博
	public function delweibo($token,$weibo_id){
        $token = urldecode($token);
        $weibo_id = trim($weibo_id);
        if(!isset($token) || !is_numeric($weibo_id))  output($this->res);
		$result = getuser_by_token($token);
		$member_id = $result["member_id"];
		$have = $this->liaoqiu_weibo_data_model->getRecordbyw_id_uid($weibo_id,$member_id);
		if($have == 0){
			$arr_return = array('state_code'=>-2,'state_desc'=>'非法操作！');
			output($arr_return); 
		}
		$data = array('w_status'=>0);		
		$result =  $this->liaoqiu_weibo_data_model->editRecordByID($weibo_id,$data);		
		$arr_return = $result ? array('state_code'=>0,'state_desc'=>'删除成功','id'=>$result) : array('state_code'=>-1,'state_desc'=>'删除失败');
		output($arr_return); 
	}
		
	//通过微博id获取微博
	public function getweibolist($token,$page_id=NULL,$page_type=NULL){
		$token = urldecode($token);
		$page_id = trim($page_id);
		$page_type = trim($page_type);
		$table = "weibo_data";
		$where = "w_status=1";
		$where .= pages($page_id, $page_type,"w_id");	
		$join = "JOIN liaoqiu_member on liaoqiu_member.member_id=a.member_id ";
		$field = "w_id,a.member_id,content,w_time,share_num,zan_num,view_num,forward_num,comment_num,member_logo,account,nick_name,hx_username,content,file_path,file_type";		
		$list = $this->base_model->select($table,$where,$field,"w_id,desc",$join,20);
		if($token!='NULL'){
			$result = getuser_by_token($token);
			$member_id = $result["member_id"];
			foreach ($list as $k => $item) {
				$true = $this->base_model->num('weibo_zan',array("member_id"=>$member_id,"weibo_id"=>$item['w_id']));
				$list[$k]['zan_status'] = $true ? 1 : 0;
			}
		} 		
		$str_id = "";
		foreach ($list as $k=>$v) {
			if($k!=0) $str_id .= ",";
			$str_id .= "'".$v['w_id']."'";
			$list[$k]['share_url'] = $this->config->item('domain')."/share/weibo/".$v['w_id'];
		}
		if(!empty($str_id)) $this->base_model->setinc($table,"w_id in ($str_id)","view_num");  //每次刷新，把当前加载的微博浏览数+1
		$arr_return = array('state_code'=>0,'state_desc'=>'成功','weibo_list'=>$list);
		output($arr_return); 
	}

	//通过微博评论  page_id为分页id ，page_type是分页类型，1是新数据，2是旧数据
	public function getweibocomment($weibo_id,$page_id=NULL,$page_type=NULL,$return=FALSE){
		$weibo_id = trim($weibo_id);
		$page_id = trim($page_id);
		$page_type = trim($page_type);
		if(!is_numeric($weibo_id))
			output($this->res);
		$where = "wc_status=1 and weibo_id=$weibo_id";
		$where .= pages($page_id, $page_type,"wid");	
		$join = "JOIN liaoqiu_member on liaoqiu_member.member_id=a.member_id ";
		$field = "wid,a.member_id,weibo_id,wc_time,zan_num,member_logo,account,nick_name,content,role";
		$comment_list = $this->base_model->select("weibo_comment",$where,$field,"wc_time,desc",$join,20);
		$result = array('comment_list'=>$comment_list,'comment_count'=>count($comment_list));
		if($return=='TRUE') return $result;
		$this->addNumByIdField($weibo_id,'view');  //自增流浏览数								
		$arr_return = array('state_code'=>0,'state_desc'=>'成功','content'=>$result);
		output($arr_return); 
	}
	
	//微博评论
	public function weibocomment($token,$weibo_id){
		$token = urldecode($token);
		$weibo_id = trim($weibo_id);
		$content = urldecode(trim($_GET['content']));
		$page_id = isset($_GET['page_id'])?trim($_GET['page_id']):'NULL';
		if(!isset($token) || !is_numeric($weibo_id) || empty($content))  output($this->res);
		$result = getuser_by_token($token);
		$member_id = $result['member_id'];
		$data = array(
				'member_id' => $member_id,
				'weibo_id' => $weibo_id,
				'content' => $content,
				'wc_time' => time(),
				'ip' => ip(),
		);
        $result =  $this->liaoqiu_weibo_comment_model->setRecord($data);
		$this->addNumByIdField($weibo_id,'comment');  //自增评论数	
		if($result){
			$page_type = $page_id!='NULL'?1:'NULL';
			$comment_list = $this->getweibocomment($weibo_id,$page_id,$page_type,'TRUE');//返回最新评论
			$arr_return = array('state_code'=>0,'state_desc'=>'评论成功','content'=>$comment_list);
			
		}else{
			$arr_return = array('state_code'=>-1,'state_desc'=>'评论失败');
		}							
		output($arr_return); 
	}
	
	//微博点赞
	public function weibozan($token,$weibo_id){
		$token = urldecode($token);
		$weibo_id = trim($weibo_id);
		if(!isset($token) || !is_numeric($weibo_id))  output($this->res);
		$result = getuser_by_token($token);
		$member_id = $result['member_id'];
		$data = array(
				'member_id' => $member_id,
				'weibo_id' => $weibo_id,
		);
		$true = $this->base_model->num("weibo_zan",$data);
		if($true==0){
	 		$data['wz_time'] = time();
			$this->load->model('liaoqiu_weibo_zan_model');
	        $true =  $this->liaoqiu_weibo_zan_model->setRecord($data);
			$this->addNumByIdField($weibo_id,'zan');  //自增评论数	
			$arr_return = $true ? array('state_code'=>0,'state_desc'=>'点赞成功') : array('state_code'=>-1,'state_desc'=>'点赞失败');
			output($arr_return); 			
		}							
		$arr_return =array('state_code'=>0,'state_desc'=>'你已点赞');
		output($arr_return); 
	}			
	
	//微博分享
	public function weiboshare($weibo_id){
		$weibo_id = trim($weibo_id);
		if(!is_numeric($weibo_id))  output($this->res);
		$result = $this->addNumByIdField($weibo_id,'share');  //自增评论数								
		$arr_return = $result ? array('state_code'=>0,'state_desc'=>'成功') : array('state_code'=>-1,'state_desc'=>'失败');
		output($arr_return); 
	}
		
	//增加函数次数
	public function addNumByIdField($id,$field){
		$field_arr = array('share','view','zan','comment','collect','forward');
		if(!in_array($field,$field_arr)) die(json_encode($this->res));
       	$result = $this->liaoqiu_weibo_data_model->addNumByIdField($id,$field.'_num');  //自增次数
		return $result;
	}	
	
}
?>