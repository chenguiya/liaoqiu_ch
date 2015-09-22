<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Share extends CI_Controller {

	/**
	 * 分享模块
	 *
	 */
	public function weibo($id)
	{
		$weibo = $this->base_model->rows('weibo_data',"w_id=$id","","JOIN liaoqiu_member ON liaoqiu_member.member_id = a.member_id ");
		$comment_list = $this->base_model->select('weibo_comment',"weibo_id=$id","","wc_time,desc","JOIN liaoqiu_member ON liaoqiu_member.member_id = a.member_id ");
		$data = array('row'=>$weibo,'comment_list'=>$comment_list);
		$this->load->view('share_weibo',$data);
	}
	
    public function show($action,$data=array())
    {
		$this->load->view($action,$data);
    }	
}
