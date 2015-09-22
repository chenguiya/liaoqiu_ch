<?php 

require_once (__DIR__ . "/Base.php");

class Weibo extends Base {

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
       	$this->load->model('liaoqiu_weibo_data_model');
    }

	//删除微博
	public function delweibo()
        {
            
            
            $return = array('state_code' => 0, 'state_desc' => '删除角色成功');

            try
            {

                $wid = $this->input->post('wid', true);

                if(empty($wid))
                {
                    throw new Exception('参数错误', 19023);
                }

                $params = array();
                $params['w_status'] = 0;

                $result =  $this->liaoqiu_weibo_data_model->editRecordByID($wid,$params);	
                if(!empty($result))
                {
                    $return = array('state_code'=>0,'state_desc'=>'删除成功','id'=>$result);
                }
                else 
                {
                    $return = array('state_code'=>-1,'state_desc'=>'删除失败');
                }


            } 
            catch (Exception $ex) 
            {
                $return['state_code'] = $ex->getCode();
                $return['state_desc'] = $ex->getMessage();
            }

            $this->format_print($return);
        
	}
		
	//通过微博id获取微博
	public function getweibolist($now_page=1){
            load_model('member');
            $result =  $this->liaoqiu_weibo_data_model->page_list(20,$now_page);
		if(!empty($result)) foreach ($result as $k=>$v) {
				$result[$k]['member_logo'] = passport_avatar_show($v['member_id'],$size = 'middle',true);	
                                $member = $this->liaoqiu_member_model->getMemberByMemberID($v['member_id']);
                                $result[$k]['account'] = isset($member['account']) ? $member['account'] : '';
		}
                
                //总数
            $count = $this->db->count_all ('weibo_data'); //取数量
            $add = 'admin/weibo/getweibolist/';
            //分页
            $page = $this->page_config($count, $now_page, $add);
            $this->data['page'] = $page['page'];
            
            $this->data['weibo_list'] = $result;
            $this->show('weibo', $this->data);
		//output($arr_return); 
	}

        
        /**
         * 发布微博
         * 
         */
        public function add()
        {
            load_model('member');
            if(!empty($_POST))
            {
                $true = FALSE;
                $msg = '';
                
                try
                {
                    $data = array();
                    $content = $this->input->post('content', true);
                    $content = !empty($content) ? trim($content) : '';
                    $strlen = mb_strlen($content, 'utf8');

                    if($strlen < 5 || $strlen > 140)
                    {
                        _E('微博内容必须在5到140个字符内', 12560);
                    }
                    
                    $data['content'] = $content;
                    
                    $member_id = $this->input->post('member_id', true);
                    if(empty($member_id))
                    {
                        _E('请选择主播', 12561);
                    }
                    
                    $member_info = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
                    if(empty($member_info))
                    {
                        _E('主播不存在', 12562);
                    }
                    
                    $data['member_id'] = $member_id;
                    $data['ip'] = ip();
                    $data['w_time'] = time();
                    if(!empty($_FILES))
                    {
                        $file = upload_file('weibo');
                        $data['file_path']=$file['file_url'];
                        $data['file_type']=$file['file_type'];
                    }
                    
                    load_model('weibo_data');
                    $result =  $this->liaoqiu_weibo_data_model->setRecord($data);	
                    if(empty($result))
                    {
                        _E('发布失败', 12563); 
                    }
                    
                } 
                catch (Exception $ex) 
                {
                    $true = false;
                    $msg = $ex->getMessage();
                }
                
                
                $true ? alert('操作成功','weibo/getweibolist',true) : alert($msg,'',true);	
            }
            else 
            {
                
                $this->data['roles'] = $this->liaoqiu_member_model->getMemberByRole(2);
                
                $this->data['title'] = '发布微博';
                $this->show('weibo_add', $this->data);
            }
        }
	
        /**
         * 修改微博
         * 
         */
        public function edit()
        {
            $w_id = !empty($_GET['w_id']) ? $_GET['w_id'] : $_POST['w_id'];
            load_model('weibo_data');
            load_model('member');
            
            
            if(!empty($_POST))
            {
                $data = array();
                $content = $this->input->post('content', true);
                $content = !empty($content) ? trim($content) : '';
                $strlen = mb_strlen($content, 'utf8');

                $true = true;
                $msg = '';
                
                try
                {
                    if($strlen < 5 || $strlen > 140)
                    {
                        _E('微博内容必须在5到140个字符内', 12560);
                    }

                    $data['content'] = $content;

                    $member_id = $this->input->post('member_id', true);
                    if(empty($member_id))
                    {
                        _E('请选择主播', 12561);
                    }

                    $member_info = $this->liaoqiu_member_model->getMemberByMemberID($member_id);
                    if(empty($member_info))
                    {
                        _E('主播不存在', 12562);
                    }

                    $data['member_id'] = $member_id;

                    if(!empty($_FILES['file']) && !empty($_FILES['file']['name']))
                    {
                        $file = upload_file('weibo');
                        $data['file_path']=$file['file_url'];
                        $data['file_type']=$file['file_type'];
                    }

                    $result = $this->liaoqiu_weibo_data_model->editRecordByID($w_id, $data);
                } 
                catch (Exception $ex) 
                {
                    $true = false;
                    $msg = $ex->getMessage();
                }
                
                $result ? alert('操作成功','weibo/getweibolist', true) : alert($msg,'',true);	
            }
            else
            {
                $this->data['title'] = '修改微博';
                $weibo_data = $this->liaoqiu_weibo_data_model->getRecordbyw_id($w_id);
                $this->data['row'] = array();
                if(!empty($weibo_data))
                {
                    $this->data['row'] = $weibo_data;
                }
                $this->data['roles'] = $this->liaoqiu_member_model->getMemberByRole(2);
                
                $this->show('weibo_add', $this->data);
            }
        }
        
        /**
         * 查看微博评论
         */
        public function view_comment()
        {
             alert('开发中。。。','weibo/getweibolist', true);
             return false;
            $this->show('weibo_comment', $this->data);
        }
        
        /**
         *  评论
         */
        public function add_comment()
        {
            
             alert('开发中。。。','weibo/getweibolist', true);
             return false;
        }

        /**
         * 查看收藏
         */
        public function view_collect()
        {
            
             alert('开发中。。。','weibo/getweibolist', true);
             return false;
            $weibo_id = $_GET['weibo_id'];
            $this->data['collect'] = array();
            
            if(!empty($weibo_id))
            {
                
            }
            $this->show('weibo_collect', $this->data);
        }
        
        /**
         * 查看点赞
         */
        public function view_zan()
        {
            
             alert('开发中。。。','weibo/getweibolist', true);
             return false;
            $this->show('weibo_zan', $this->data);
        }
        
        
}
?>