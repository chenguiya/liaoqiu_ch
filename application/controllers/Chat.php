<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    private $res = array(
        "state_code"    =>    -99,
        "state_desc"    =>    "参数有误",
    );
	
    public function __construct()
    {
        parent::__construct();
        verify_sign();
    }
	
	//举报
    public function jubao($token, $bejubao, $show_id, $msguuid, $content) {
        $token = urldecode($token);
        $bejubao = urldecode($bejubao);
        $show_id = urldecode($show_id);
        $msguuid = urldecode($msguuid);
        $content = urldecode($content);

        //判断参数完整性
        if(!isset($token) || !isset($bejubao) || !isset($show_id) || !isset($msguuid) || !isset($content)) {
            output($this->res);
        }

        if($show_id=="NULL"){
            $show_id = "";
        } else if(!is_num($show_id)) {
            $this->res["state_desc"] = "show_id错误";
            output($this->res);
        }

        if(countstring($content) < 10 || countstring($content) > 200) {
            $this->res["state_desc"] = "举报理由格式不对，10~100个字";
            output($this->res);
        }

        //校验token, 并顺便取用户基本信息
        $result = getuser_by_token($token);
        $member_id = $result["member_id"];
        $param = array(
        			"member_id" => $member_id,
        			"show_id" => $show_id,
        			"chatlogs_uuid" => $msguuid,
        			"beijubao_member_id" => $bejubao,
        			"content" => $content,
        			"jubao_time" => time(),
        			"operate_status" => 0,
        );

        $this->load->model('liaoqiu_jubao_model');
        $result_array = $this->liaoqiu_jubao_model->setRecord($param);
        if($result_array < 0){
            $this->res["state_desc"] = "你已经举报过此条消息，不需要重复举报";
            output($this->res);
        }
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "举报成功，请等待主播处理";
        output($this->res);
    }

	//获取用户曾经举报过的记录
    public function getjubaolist($token) {
        $token = urldecode($token);
        //判断参数完整性
        if(!isset($token)) output($this->res);
        //校验token, 并顺便取用户基本信息
        $result = getuser_by_token($token);
        $member_id = $result["member_id"];
        $this->load->model('liaoqiu_jubao_model');
        $result_array = $this->liaoqiu_jubao_model->getRecordsByMemberID($member_id);
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "操作成功";
        $this->res["jubao_list"] = $result_array;
        output($this->res);
    }
	
	//用户点赞
    public function zan($token, $beizan=NULL, $show_id, $msguuid, $type) {
        $token =  trim(urldecode($token));
        $beizan = trim($beizan);
        $show_id = trim($show_id);
        $msguuid = trim(urldecode($msguuid));
        $type = trim($type);

        //判断参数完整性
        if(!isset($token) || !isset($beizan) || !isset($show_id) || !isset($msguuid)) {
            $this->res["state_desc"] = "参数错误";
            output($this->res);
        }

        if($show_id=="NULL"){
            $group_id = "";
        } else if(!is_num($show_id)) {
            $this->res["state_desc"] = "show_id错误";
            output($this->res);
        }

        if($type!="0" && $type!="1"){
            $this->res["state_desc"] = "type错误";
            output($this->res);
        }

		if(!is_numeric($beizan)) $beizan="0";

        //校验token, 并顺便取用户基本信息
        $result = getuser_by_token($token);
        $member_id = $result["member_id"];

        $param = array();
        $param["member_id"] = $member_id;
        $param["show_id"] = $show_id;
        $param["hx_msgid"] = $msguuid;
        $param["beizan_member_id"] = $beizan;
        $param["zan_time"] = time();
        $param["status"] = $type;
		$num = $this->base_model->num('show_chatlogs_zan',array("show_id"=>$show_id,"member_id"=>$member_id,"hx_msgid"=>$msguuid));
		if($num==0){
			$result = $this->base_model->insert('show_chatlogs_zan',$param);
			if($result) $this->base_model->setinc('chatlogs',"hx_msgid ='".$msguuid."'","zan_num");
			$this->res["state_desc"] = "操作成功";			
		}else{
			 $this->res["state_desc"] = "您已经点赞";
		}
		$this->res["state_code"] = 0;
        output($this->res);
    }

	//获取某条聊天记录的点赞数
    public function getmsgzan($msguuid) {
        $msguuid = urldecode($msguuid);
        if(!isset($msguuid)) output($this->res);
		$where = array('hx_msgid' => $msguuid, 'status'=>"1");
        $result = $this->base_model->num('show_chatlogs_zan',$where);
        $this->res["state_code"] = 0;
        $this->res["state_desc"] = "操作成功";
        $this->res["zan_data"] = $result;
        output($this->res);
    }
	
	//获取聊天记录，通过接收者IDb_id，时间起始,（时间为空则取最近的20条）
	public function getchatlogsbyshowid($b_id,$start_time=NULL){
        $b_id = urldecode($b_id);
        $start_time = urldecode($start_time);
		//判断参数完整性
        if(empty($b_id)) output($this->res);
		$this->load->model('liaoqiu_chatlogs_model');
		$logs = $this->liaoqiu_chatlogs_model->getRecordsByBidStartTime($b_id,$start_time);
		isEmpty($logs);  //如果为空则终止
		foreach ($logs as $k => $v) {
				unset($logs[$k]['hx_password']);
				unset($logs[$k]['token']);
				unset($logs[$k]['token_time']);
				unset($logs[$k]['add_time']);
				if($v['file_id']){
					$file = $this->base_model->row('file',array('id'=>$v['file_id']));
					if(!empty($file)) $logs[$k]['file_content'] = $file;
				}//if($file_id) 
		}
		$res = array('state_code'=>0,'state_desc'=>'成功','chatlogs_list'=>$logs);
		output($res);
	}
	
	//通过节目ID获取聊天记录	
    public function export_chatlogs($show_id, $type) {
    	$this->load->helper('huanxin');
        $show_id = urldecode($show_id);     //节目时，此为节目ID，单聊时，此为memberid
        $type = urldecode($type);   //1为单聊，2为节目
        //判断参数完整性
        if(!isset($show_id) || !isset($show_id)) output($this->res);
        if(!is_num($show_id)) {
            $this->res["state_desc"] = "show_id错误";
            output($this->res);
        }
        if($type!="1" && $type!="2") {
            $this->res["state_desc"] = "type错误";
            output($this->res);
        }
		
        $this->load->model('liaoqiu_show_model');
        $this->load->model('liaoqiu_file_model');
        $this->load->model('liaoqiu_chatlogs_model');
		$this->load->model('liaoqiu_chatlogs_timestamp_model');
		//取时间戳
        $timestamp_result = $this->liaoqiu_chatlogs_timestamp_model->getRecordByObjectIDType($show_id, $type);
		$where = "";
		$now = time();
        if(!empty($timestamp_result)){
            $timestamp = $timestamp_result["timestamp"]."000";
            $getlogs_time = $timestamp_result["getlogs_time"];
            $times_sub = $now - $getlogs_time;
            if($times_sub < 60){
                $this->res["state_code"] = -4;
                $this->res["state_desc"] = "间隔1分钟取一次";
                //output($this->res);
            }
            //$where .= "+and+timestamp+>+".$timestamp;
        }
		
        //去环信取记录
        if($type=="1"){  //单聊
            $sql = "select+*+where+from='5usport_".$object_id."'".$where."+order+by+timestamp+desc";  //&limit=20";
        } else {  //群聊
            $result = $this->liaoqiu_show_model->getAvalableRecordByID($show_id);
            if(empty($result)){
                $this->res["state_code"] = -4;
                $this->res["state_desc"] = "该节目不存在或已经删除";
                output($this->res);
            }
            $hx_room_id = $result["hx_room_id"];
			$limit = 20;
			$cursor = !empty($timestamp_result["cursor"])?$timestamp_result["cursor"]:'';
            $sql = "select+*+where+to='".$hx_room_id."'&limit=$limit&cursor=".$cursor;  //&limit=20";
        }
		$tmp_params = array("sql"=>$sql);
		$hx_action = "exportlog";
		$huanxin_result = huanxin($hx_action, $tmp_params);
		huanxin_result($huanxin_result, $hx_action);
		if($huanxin_result["huanxin_result"]["entities"]=="") {
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "服务器繁忙，请稍候重试";
            output($this->res);
        }
        $arrlogs = $huanxin_result["huanxin_result"]["entities"];
		/* 
		echo '<pre>';
		print_r($arrlogs);
		die;
		*/
		//写chatlogs表
       foreach ($arrlogs as $k => $v) {
       	 if($v["payload"]["bodies"][0]["type"]!="cmd"  //过滤透传消息
       	  || (isset($timestamp)&&$timestamp<@$v["payload"]["bodies"][0]["timestamp"])) {   //过滤已经 获取的消息
       	  		$from_id = $v["from"]=='admin'?11:substr($v["from"],8);//自定义用户 //5usport_为8个字符，从第8个开始读取
                if(!is_numeric($from_id)){
                	$this->res["state_code"] = -1;
		            $this->res["state_desc"] = "用户id错误".$from_id;
		            output($this->res);                	
                }
	            //1为文字消息，2为图片消息，3为语音消息，4为视频，5为位置消息
	            $content_type_arr = array("img" =>"2","audio" =>"3","txt" =>"1","video" =>"4","loc" => "5",);
				$content_type = $content_type_arr[$v["payload"]["bodies"][0]["type"]];	            
		        $file_id = "0";
	            //如果是倒序desc的话，第一条($k==0)是最新的记录，需要取最新记录的时间来写到timestamp里
	            if($k==count($arrlogs)-1) $timestamp = substr($v["created"], 0, 10);  //时间是10位
	            if($content_type!=1 && $content_type!=5) {
	                //需要写文件表
	                $arr_file_param = array();
	                $arr_file_param["member_id"] = $from_id;
	                $arr_file_param["hx_uuid"] = $v["uuid"];
	                $arr_file_param["hx_url"] = @$v["payload"]["bodies"][0]["url"];
	                $arr_file_param["hx_type"] = $v["payload"]["bodies"][0]["type"];
	                $arr_file_param["hx_filename"] = @$v["payload"]["bodies"][0]["filename"];
	                $arr_file_param["hx_secret"] = $v["payload"]["bodies"][0]["secret"];
	                $arr_file_param["hx_thumbnail_url"] = @$v["payload"]["bodies"][0]["thumb"];
	                $arr_file_param["width"] = @$v["payload"]["bodies"][0]["size"]["width"];
	                $arr_file_param["height"] = @$v["payload"]["bodies"][0]["size"]["height"];
	                $arr_file_param["zan"] = "0";
	                $arr_file_param["file_size"] = @$v["payload"]["bodies"][0]["file_length"];
	                $arr_file_param["length"] = @$v["payload"]["bodies"][0]["length"];
	                $arr_file_param["uploader_time"] = $v["created"];
	                $file_id = $this->liaoqiu_file_model->setRecord($arr_file_param);
					if($file_id>0) $this->file_download($file_id, $content_type);  //把文件从环信下载到5U
	            }// if($v["payload"]["bodies"][0]["type"]!="txt")
	            
	            //写chatlogs表
	            $arr_chatlogs_param = array();
	            $arr_chatlogs_param["a_id"] = $from_id;
	            $arr_chatlogs_param["b_id"] = $show_id;
	            if($file_id >= 0){
	                $arr_chatlogs_param["file_id"] = $file_id;
	            }
	            $arr_chatlogs_param["hx_msgid"] = $v["msg_id"];
	            $arr_chatlogs_param["type"] = $type;
	            $arr_chatlogs_param["content_type"] = $content_type;
	            $arr_chatlogs_param["content"] = @$v["payload"]["bodies"][0]["msg"];
	            $arr_chatlogs_param["chattime"] = substr($v["created"], 0, 10);
	            $arr_chatlogs_param["recommend"] = "0";
				$arr_chatlogs_param["lng"] = @$v["payload"]["bodies"][0]["lng"];
				$arr_chatlogs_param["lat"] = @$v["payload"]["bodies"][0]["lat"];
				$arr_chatlogs_param["addr"] = @$v["payload"]["bodies"][0]["addr"];
	            $operate_id = $this->liaoqiu_chatlogs_model->setRecord($arr_chatlogs_param);
			}//if($v["payload"]["bodies"][0]["type"]!="cmd") {  
		}//foreach ($arrlogs
        //更新chatlogs_timestamp表
        $arr_tamp_param = array();
        $arr_tamp_param["object_id"] = $show_id;
        $arr_tamp_param["type"] = $type;
        $arr_tamp_param["timestamp"] = substr($timestamp,0,10);
        $arr_tamp_param["getlogs_time"] = $now;
		$arr_tamp_param["cursor"] = @$huanxin_result["huanxin_result"]["cursor"];
        $operate_id = $this->liaoqiu_chatlogs_timestamp_model->setRecord($arr_tamp_param);
        $this->res["state_code"] = "0";
        $this->res["state_desc"] = "操作成功";
        output($this->res);				
	}

    public function file_download($file_id, $type) {
        $file_id = urldecode($file_id);
        $type = urldecode($type);   //文件类型，可以为空。2为图片，3为语音，4为视频
        if(!is_num($file_id)) output($this->res);
        $arr_file_type = array(
            "img" =>  ".jpg",
            "audio" =>  ".amr",
            "video" =>  ".mp4",
        );
        //判断格式
        if($type!="NULL"&&$type!="2"&&$type!="3"&&$type!="4"){
            $this->res["state_desc"] = "type错误";
            output($this->res);
        }
        $this->load->model('liaoqiu_file_model');
        $file_array = $this->liaoqiu_file_model->getRecordByID($file_id);
		
		if($file_array["5u_url"]==""){  //应该在获取聊天记录的时候下载
            //通过环信接口下载文件
            $hx_type_dir_name = $file_array["hx_type"];
            $lq_uploads_path = "upload/chat/".$hx_type_dir_name;
            if(!is_dir($lq_uploads_path)) {
                mkdir($lq_uploads_path, 0775, true);  
            }
            $file_name = $file_array["uploader_time"]."_".$file_array["member_id"];
            
            $tmp_params = array();
            $tmp_params["file_name"] = $file_name;
            $tmp_params["file_type"] = $arr_file_type[$hx_type_dir_name];
            $tmp_params["path"] = $lq_uploads_path;
            $tmp_params["hx_url"] = $file_array["hx_url"];
            $tmp_params["secret"] = $file_array["hx_secret"];
            $tmp_params["thumb"] = "0";
			$hx_action = "dlfiles";
            $huanxin_result = huanxin($hx_action, $tmp_params);
            $file_name .= $tmp_params["file_type"];
			$path = "/upload/chat/".$hx_type_dir_name."/".$file_name;
			$dir = ".".$path;
            $file_5u_dl_url = "http://".$_SERVER["HTTP_HOST"].$path;
			$file_downloaded = false;
            for($i=0; $i<3; $i++){  //检查是否下载成功
                if(!file_exists($dir)){
                    $huanxin_result = huanxin($hx_action, $tmp_params);
                } else {
                    $file_downloaded = true;
                    break;
                }// if 
            } //for 
			if($huanxin_result["code"]!="0"){
                $this->res["state_desc"] = "下载文件失败，环信服务器繁忙";
				echo $this->res["state_desc"];
				$file_5u_dl_url = $file_array["hx_url"];  //如果下载失败，用环信地址
                //output($this->res);
            }
		
			if($file_array["hx_type"]!="audio"){
				//下载缩略图
				$tmp_params["thumb"] = "1";
	           	$file_name = "thumb_".$file_array["uploader_time"]."_".$file_array["member_id"];
				$tmp_params["file_name"] = $file_name;
				$tmp_params["file_type"] = ".jpg";
				if($file_array["hx_thumbnail_url"]!="") //如果是视频，这里不为空
					$tmp_params["hx_url"] = $file_array["hx_thumbnail_url"];
				$huanxin_result = huanxin($hx_action, $tmp_params);
				$file_name .= $tmp_params["file_type"];
				$path = "/upload/chat/".$hx_type_dir_name."/".$file_name;
				$dir = ".".$path; 
				$thumbnail_url = "http://".$_SERVER["HTTP_HOST"].$path;
				if(!file_exists($dir)){
	                    $thumbnail_url = $file_5u_dl_url;  //如果下载失败，用大图地址
	            }				
			}//if($file_array["hx_type"]!="audio")

			//写file表
            $arr_file_param = array("5u_url"=>$file_5u_dl_url,"5u_thumbnail_url"=>@$thumbnail_url);
            $file_id = $this->liaoqiu_file_model->editRecordByID($file_id, $arr_file_param);
       }else{
            $file_5u_dl_url = $file_array["5u_url"];
       }//  if($file_array["5u_url"]=="")
		
		
	}	
	
}

?>	