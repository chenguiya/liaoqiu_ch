<?php

class MY_Loader extends CI_Loader {

    private $_CI;
    private $arr_name;

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->_CI =& get_instance();
        $this->arr_name = array('viewrankings'  => '热度排行',
                                'events'        => '赛事预告',
                                'nba_rank'        => '篮球排行',
                                'nba_events'        => '今日比赛录像',
			     'nba_lx' => '比赛录像',
                                'sporttidbits'  => '花花体坛',
                                'recommended'   => '专栏推荐',
                                'sportvideo'    => '体育视频',
                                'visionframe'   => '体育视觉',
                                'sporttidbits2' => '花花体坛',  // 花花体坛2
                                'events_align'  => '赛事预告',  // 赛事预告 横排
                                'memberbar'     => '用户Bar',
                                'ad' => '推荐专题'   // elite_special 改名 ad
                                );
        $this->_CI->config->load('common_config');
    }

    /**
     * 显示公共页面组件
     * @param $name 组件名字
     * @param $title 组件标题
     * @param $arr 组件的其他参数
     * @return NULL
     */
    public function showComponent($name = 'sporttidbits', $title = '', $params = array())
    {
        $class_name = $name;
        $class_names = get_class_methods($this);
        if(in_array($class_name, $class_names))
        {
            $arr['name'] = $name;
            $arr['title'] = $title;
            if (isset($params)) {
            	foreach ($params as $key => $value) {
            		$arr[$key] = $value;
            	}
            }
            $this->$class_name($arr);
        }
    }

    // 推荐专题
    private function ad($arr)
    {
        $this->_CI->load->model('special_model');
        $data['tjzt'] = $this->_CI->special_model->getEliteSpecial(1);
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    // 花花体坛2
    private function sporttidbits2($arr)
    {
        $navigation = $this->_CI->config->item('navigation');
        $this->_CI->load->model('position_model');
        $data['focuspic'] = $this->_CI->position_model->position(142, 5);
        $qiniu_use = $this->_CI->config->item('qiniu_use');
        if($qiniu_use){
            $img_data = array();
            $img_data['is_promote'] = 2;
            $img_data['mode'] = 1;
            $img_data['width'] = 300;
            $img_data['height'] = 165;
            $data['focuspic'] = qiniu_thumb_store_general($data['focuspic'], $img_data,'home_picture');
        }
        $data['more_url'] = $navigation['gossip']['花边'];
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    // 花花体坛
    private function sporttidbits($arr)
    {
        $navigation = $this->_CI->config->item('navigation');
        $this->_CI->load->model('position_model');
        $this->_CI->load->model('news_model');
        $data['focuspic'] = $this->_CI->position_model->position(142, 5);        // 焦点图
        $qiniu_use = $this->_CI->config->item('qiniu_use');
        if($qiniu_use){
            $img_data = array();
            $img_data['is_promote'] = 2;
            $img_data['mode'] = 1;
            $img_data['width'] = 300;
            $img_data['height'] = 165;
            $data['focuspic'] = qiniu_thumb_store_general($data['focuspic'], $img_data,'home_picture');
        }
       // $data['gossipphoto'] = $this->_CI->position_model->position(144,6);  // 花边酷图小图
        $data['gossipphoto'] = $this->_CI->news_model->lastChannelNews(6, 120);  // 最新花边新闻
        $data['more_url'] = $navigation['gossip']['花边'];
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

// 热度排行
    private function viewrankings($arr)
    {

        $this->_CI->load->model('hits_model');
        if(@$arr['catid'] != '')
        {
            if($arr['catid'] == 12 || $arr['catid'] == 7 || $arr['catid'] == 6) // 三个聚合
            {
                $data['rdph'] = $this->_CI->hits_model->getRankList(10, 'views', $arr['catid']+0, 2);
            }
            else
            {
                $data['rdph'] = $this->_CI->hits_model->getRankList(10, 'views', $arr['catid']+0);
            }
        }
        else
        {
            $data['rdph'] = $this->_CI->hits_model->getRankList(10);
        }
        $data['title'] = $arr['title'];
        if($data['title'] == '')
            $data['title'] = '热度排行';

        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    //专家热文排行
    private function hotBlogRankings($params)  {
    	$this->_CI->load->model('blog_model');
		$result_id = $this->blog_model->getBlogList(array('username'=>$params['username']), 'id');
		if (!$result_id) return FALSE;
		foreach ($result_id as $rid) {
			$id_arr[] = "'c-24-{$rid->id}'";
		}
		$all_ids = implode(',', $id_arr);
		$this->_CI->load->model('hits_model');
		$result = $this->hits_model->getWhereRank("hitsid IN ({$all_ids})", 10, 'views');
		if ($result) {
			foreach ($result as $r) {
				$pos = strpos($r->hitsid,'-',2) + 1;
				$ids_array[] = $id = substr($r->hitsid,$pos);
				$hits[$id] = $r;
			}
			$ids = implode(',', $ids_array);
			$data['hotblogs'] = $this->blog_model->getBlogList("id IN ({$ids})", 'title,url,description');
			$tpl = strtolower($params['name']);
			$this->_CI->load->view('component/'.$tpl, $data);
		} else {
			return FALSE;
		}
    }


    // 赛事预告
    private function events($arr)
    {
        $this->_CI->load->model('event/match_model');
        $data['ssyg'] = $this->_CI->match_model->detailMatchList(12);
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }
	
    // nba录像
    private function nba_events($arr)
    {
        $this->_CI->load->model('event/match_model');
        $data['events'] = $this->_CI->match_model->getMatchList('a_id,b_id,a_name,b_name,match_time','scd_match','event_id=2  AND display=1 AND league_id=7',0,12,'match_time DESC');
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }
	
	private function nba_lx($arr) 
	{
		$this->_CI->load->model('video_model');
		$cateid_arr = $this->_CI->category_model->getCategoryCfg();
		$nba_cateid = $cateid_arr['video']['nba_lx'];
		if (!$nba_cateid) {
			$nba_cateid = 16;
		}
		$data['nbalx'] = $this->_CI->video_model->rankVideo(10,$nba_cateid);
		$this->_CI->load->view('component/'.$arr['name'], $data);
	}
	private function nba_rank($arr)
	{
		
		$this->_CI->load->model('video_model');
		$this->_CI->load->model('hits_model');
		$this->_CI->load->model('category_model');
		
		$cateid_arr = $this->_CI->category_model->getCategoryCfg();
		$nba_cateid = $cateid_arr['video']['nba_lx'];
		if (!$nba_cateid) {
			$nba_cateid = 16;
		}
		$data['rank'] = $this->_CI->video_model->rankVideo(10,$nba_cateid,"dayviews");
		$data['first_rank'] = array_shift($data['rank']);
		$data['hits']=$this->_CI->hits_model->getHits($data['first_rank']->id,10)->views ;
		$data['hits']=$data['hits']?$data['hits']:0; 
		$this->_CI->load->view('component/'.$arr['name'], $data);
	}

	// 横排的赛事预告
    private function events_align($arr)
    {
        $this->_CI->load->model('event/match_model');
        $data['ssyg'] = $this->_CI->match_model->detailMatchList(12);
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    // 专栏推荐
    private function recommended($arr)
    {
        $this->_CI->load->model('position_model');
        $this->_CI->load->model('blog_model');
        $viewposition = $this->_CI->position_model->getPositionData(180, 5);
        foreach ($viewposition as $key=>$val) {
			$row = $this->_CI->blog_model->getBlog($val['id']);
        	$viewposition[$key]['author_avatar'] = getucavatarbyusername($row->username);
        	$viewposition[$key]['author_nickname'] = getmemberrealname($row->username, 0);
        	$viewposition[$key]['url'] = $row->url;
        	$viewposition[$key]['userid'] = get_userinfo_username($row->username, 'userid')->userid;
        }
//         var_dump($viewposition);die;
        $data['zltj'] = $viewposition;
        $navigation = $this->_CI->config->item('navigation');
        $data['more_url'] = $navigation['view']['观点'].'expert';

        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    // 体育视频
    private function sportvideo($arr)
    {
        $navigation = $this->_CI->config->item('navigation');
        $data['more_url'] = $navigation['video']['视频'];
        $this->_CI->load->model('video_model');
        $data['tysp'] = $this->_CI->video_model->lastVideos(4);
// 		$data['tysp'] = $this->_CI->video_model->rankVideo(4);
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    // 体育视觉/性感体坛/明星八卦
    private function visionframe($arr)
    {
        $navigation_sub = $this->_CI->config->item('navigation_sub');
        $navigation = $this->_CI->config->item('navigation');

        $this->_CI->load->model('position_model');
        $data['title'] = $arr['title'] != '' ? $arr['title'] : $this->arr_name[$arr['name']];
        if($data['title'] == '体育视觉')
        {
            $_data = $this->_CI->position_model->position(147, 1);
            $data['leftpic'] = $_data[0];                                           //体育视觉左边图
            $data['middlepic'] = $this->_CI->position_model->position(148, 2);      //体育视觉2：1图片
            $data['middleonepic'] = $this->_CI->position_model->position(149, 2);   //体育视觉1：1图片 体育视觉-中正2
            $_data = $this->_CI->position_model->position(150, 1);
            $data['rightpic'] = $_data[0];                                          //体育视觉1：1图片
            $data['more_url'] = $navigation['pic']['图库'];
        }
        elseif($data['title'] == '性感体坛')
        {
            $_data = $this->_CI->position_model->position(171, 1);                  // 左
            $data['leftpic'] = $_data[0];
            $data['middlepic'] = $this->_CI->position_model->position(172, 2);      // 中长2
            $data['middleonepic'] = $this->_CI->position_model->position(173, 2);   // 中正2
            $_data = $this->_CI->position_model->position(174, 1);                  // 右
            $data['rightpic'] = $_data[0];
            $data['more_url'] = $navigation_sub['pic'][$data['title']];
        }
        elseif($data['title'] == '明星八卦')
        {
            $_data = $this->_CI->position_model->position(167, 1);                  // 左
            $data['leftpic'] = $_data[0];
            $data['middlepic'] = $this->_CI->position_model->position(168, 2);      // 中长2
            $data['middleonepic'] = $this->_CI->position_model->position(169, 2);   // 中正2
            $_data = $this->_CI->position_model->position(170, 1);                  // 右
            $data['rightpic'] = $_data[0];
            $data['more_url'] = $navigation_sub['pic'][$data['title']];
        }
        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

	public function comment($arr)
	{
			if(!$arr['commentid'])
			{
				return 0;
			}
			$data['commentid']=$arr['commentid'];
			$this->_CI->load->view('component/'.$arr['name'], $data);
	}

	public function link($arr)
	{
			if(!$arr['typeid'])
			{
				return 0;
			}
			$this->_CI->load->model('link_model');
			$data['link']=$this->_CI->link_model->getTypeLinks($arr['typeid'],$arr['num']);
			$this->_CI->load->view('component/'.$arr['name'], $data);
	}

	public function sporttag($arr)
	{
		$this->_CI->load->model('note_model');
		$data['tag']=$this->_CI->note_model->getTypeNotes(12,24);
		$this->_CI->load->view('component/'.$arr['name'], $data);
	}

    // 用户BAR
    public function memberbar($arr)
    {
        $arr_member = get_member_info();
        $have_login = $arr_member['user_id'] + 0 > 0 ? TRUE : FALSE;

        $data['have_login'] = $have_login;
        $data['arr_member'] = $arr_member;
        $data['group_url'] = $this->_CI->config->item('group_url');
        $data['home_url'] = $this->_CI->config->item('home_url');
        /*  UC的
            注册地址：http://discuz.usport.cc/member.php?mod=register&referer=[返回页面url]
            会员中心：http://discuz.usport.cc/home.php?mod=space&uid=19&do=profile
            资料修改：http://discuz.usport.cc/home.php?mod=spacecp
        */

        $this->_CI->load->view('component/'.$arr['name'], $data);
    }

    public function getimgfromcontent($modelid = 1, $contentid)  {
    	$this->_CI->load->model('model_model');
    	$MODELS = $this->_CI->model_model->getAll();
    	$tablename = $MODELS[$modelid]->tablename;
		$modelname = strtolower($tablename).'_model';
    	$this->_CI->load->model($modelname);
    	$mothedname = 'get'.ucfirst($tablename).'Data';
    	$result = $this->_CI->$modelname->$mothedname($contentid);
    	$content = $result->content;
    	$url = $result->url;
    	preg_match_all('/<img alt="(.*?)" src="(.*?)" (.*?)>/', $content, $match);

//     	$text_arr = $match[1];
    	$img_arr = $match[2];
    	$str = '';
    	if (count($img_arr)>0) {
    		foreach ($img_arr as $k=>$vo) {
    			$str .= '<a href="'.$url.'" target="_blank"><img alt="'.$text_arr[$k].'" src="'.$vo.'" width="157" height="118" border="0"></a>';
    		}
    		return $str;
    	} else {
    		return FALSE;
    	}
    }
}