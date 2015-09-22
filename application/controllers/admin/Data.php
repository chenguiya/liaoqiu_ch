<?php 

require_once (__DIR__ . "/Base.php");

class Data extends Base {

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
		$this->load->model('liaoqiu_member_model');
    }
	
	//举报节目聊天记录
	public function chat_report($now_page=1){
		$add = 'admin/show/chat_report/';
		$count = $this->db->count_all ('jubao'); //取数量
		$page = $this->page_config($count, $now_page, $add);
		if(isset($_GET['show_id'])) $where =  "show_id=".trim($_GET['show_id']);	
		$this->load->model('liaoqiu_jubao_model');
		$report_list = $this->base_model->page($page['list_page'],$now_page,'jubao',@$where,'','jubao_time,desc');
		$report	= array();
		if(!empty($report_list)){
					$uid_str = ''; 
					foreach ($report_list as $k=>$v) {
						$uid_str .= $v['member_id'].",".$v['beijubao_member_id'].",";
					}//
					$uid_str = substr($uid_str,0,strlen($uid_str)-1);
					$member_arr = $this->liaoqiu_member_model->getMemberByManyMemberID($uid_str);
					$nickname_arr = array();
					foreach ($member_arr as $member) {
						$nickname_arr[$member['member_id']] = $member['account'];
					}//
					foreach ($report_list as $item) {
						$item['member_id'] = $nickname_arr[$item['member_id']];
						$item['beijubao_member_id'] = $nickname_arr[$item['beijubao_member_id']];
						$report[] = $item;
					}//
					unset($report_list);			
		}//if(!empty($report_list))
		$this->data['report_list'] = $report;
		$this->data['page'] = $page['page'];
		$this->show('chat_report',$this->data);			
	}

	//处理举报节目聊天记录
	public function see_chat_report($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$id = trim($id);
		$this->load->model('liaoqiu_jubao_model');
		$data = array('operate_status'=>1);
		$result = $this->liaoqiu_jubao_model->editByID($id,$data);
		$result ? alert('操作成功','data/chat_report') : alert('操作失败','',true);		
	}
			
	//反馈列表
	public function feedback($now_page=1){
		$table = 'feedback';
		$list = $this->base_model->page(20,$now_page,$table,'','','time,desc');
		foreach ($list as $k => $v) {
			$member = $this->base_model->row('member',array('member_id'=>$v['member_id']));
			$list[$k]['member'] = !empty($member)?$member['account']:'未知';
			if($v['type']==1){
				$list[$k]['type'] = '苹果';
			}else if($v['type']==2){
				$list[$k]['type'] = '安卓';
			}else{
				$list[$k]['type'] = '未知';
			}
		}
	    $count = $this->base_model->num($table); //取数量
	    $add = 'admin/member/index/';
	    //分页
	    $page = $this->page_config($count, $now_page, $add);
	    $this->data['page'] = $page['page'];
	    $this->data['list'] = $list;
	    $this->show('feedback', $this->data);
	}
		
	//设为反馈已处理
	public function deal_with($id){
		if(!is_numeric($id)) alert('参数错误','',true);
		$id = trim($id);
		$true = $this->base_model->update('feedback',array('id'=>$id),array('status'=>1));
		$true ? alert('操作成功','data/feedback') : alert('操作失败','',true);		
	}
	
}
?>