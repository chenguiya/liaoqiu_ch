<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crontab extends CI_Controller {

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
		//$this->load->library('memcache');
        verify_sign();
    }

	//获取聊天记录
    public function export_chatlogs($type) {
        $type = urldecode($type);   //1为单聊，2为群聊
        //判断参数完整性
        if(!isset($type)) output($this->res);
        $arr_export_type = array(
            "1" => "从环信导出单聊聊天记录",
            "2" => "从环信导出所有官方群的聊天记录",
            "3" => "从环信导出所有群的聊天记录",
        );
        if(!array_key_exists($type, $arr_export_type)) {
            $this->res["state_desc"] = "type错误";
            output($this->res);
        }
        $this->load->helper('huanxin');
        $this->load->model('liaoqiu_show_model');
        $sql = "";
        switch($type){
            case "1":
                $this->res["state_desc"] = "暂不支持导出单聊聊天记录";
                output($this->res);
                break;
            case "2":
				$show_array = $this->liaoqiu_show_model->getAllStartRecord();  //获取正在开始的节目
                break;
			default:
				$this->res["state_desc"] = "type错误";
            	output($this->res);
				break;
        }
        if(empty($show_array)){
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "没有需要操作的群";
            output($this->res);
        }
        foreach ($show_array as $v) {
            $request = "chat/export_chatlogs/".$v['id']."/2";
            $str_ret = lq_api($request, array());
            echo $str_ret;
        }
    }

	//去环信下载文件
    public function file_download() {
        //step1:判断参数完整性
        //step2:读取当前尚未下载的文件
        $sql = "";
        $time_min = time() - (3600*24*300);       //取三天以内尚未下载的文件
        $sql = "select * from liaoqiu_file where uploader_time > ".$time_min."000 and 5u_url IS NULL";
        $this->load->model('liaoqiu_file_model');
        $file_array = object_array($this->liaoqiu_file_model->getRecordBySQL($sql));
        if(count($file_array)==0){
            $this->res["state_code"] = -4;
            $this->res["state_desc"] = "没有需要操作的文件";
            output($this->res);
        }
        $arr_object_type = array(
            "audio" => "3",
            "video" => "4",
            "img" => "2",
        );
        foreach ($file_array as $k => $v) {
            $request = "chat/file_download/".$v["id"]."/".$arr_object_type[$v["hx_type"]];
            $str_ret = lq_api($request, array());
            echo $str_ret;
        }
    }

}
?>