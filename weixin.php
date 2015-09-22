<?php
//define your token
define("TOKEN", "zJRvVwELniRDeGSgx8egHU4hfR6W");//改成自己的TOKEN
define('APP_ID', 'wx9f053140e40aa237');//改成自己的APPID
define('APP_SECRET', '6f53c5d5ec1fdeb2a4f36d9612019f46');//改成自己的APPSECRET

$action = @$_REQUEST["action"];
$arr_action = array("createMenu", "valid", "responseMsg", "deleteMenu");
$wechatObj = new wechatCallbackapiTest(APP_ID,APP_SECRET);

if(empty($action)){
    $wechatObj->responseMsg();
    die;
}

if(in_array($action, $arr_action)){
    $wechatObj->$action();
} else {
    $wechatObj->responseMsg();
}


class wechatCallbackapiTest
{
    private $FromUserName;
    private $ToUserName;
    private $MsgType;
    private $Event;
    private $EventKey;
    private $times;
    private $keyword;
    private $app_id;
    private $app_secret;
    private $postObj;
    private $str_order = "请发送以下指令？\n1:最新活动；\n2:下载聊球app；\n3:聊球二维码；\n5:帮助说明";
    private $str_help = "帮助说明:\n除发送指令外, 本公众号提供直播聊天室互动功能, 以及发送小视频和球友一起分享功能, 另外还增加了线下球迷会活动的相关功能;\n聊天室互动和球迷会活动可通过公众号菜单选择. 发小视频功能, 则通过输入框选择小视频拍摄并发送, 等系统回复'添加文字说明'提示后, 进入链接输入说明文字即可.";
    private $arr_t_f = array("否","是");
    private $arr_m_f = array("秘密","男","女");
    private $str_domain = "http://www.5ulq.com/";
    //private $debug_wx_id = "oidcSwjUmM1P2RNXaCURP4pW-Cg0";
    private $debug_wx_id = "libo";
    //加载文字模板
    private $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        <FuncFlag>0</FuncFlag>
        </xml>";
    //加载图文模版
    private $picTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[%s]]></MsgType>
         <ArticleCount>1</ArticleCount>
         <Articles>
         <item>
         <Title><![CDATA[%s]]></Title>
         <Description><![CDATA[%s]]></Description>
         <PicUrl><![CDATA[%s]]></PicUrl>
         <Url><![CDATA[%s]]></Url>
         </item>
         </Articles>
         <FuncFlag>1</FuncFlag>
		</xml>";
	//加载音乐模版
	private $mscTpl= "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[music]]></MsgType>
		<Music>
		<Title><![CDATA[TITLE]]></Title>
		<Description><![CDATA[DESCRIPTION]]></Description>
		<MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
		<HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
		</Music>
		<FuncFlag>1</FuncFlag>
		</xml>";

    public function __construct($appid,$appsecret)
    {
        $this->app_id = $appid;
        $this->app_secret = $appsecret;
    }

    //签名验证公共接口
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
           echo $echoStr;
           exit;
        }
    }

    //主入口处理函数
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        if (!empty($postStr)){
            $this->postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->FromUserName = $this->postObj->FromUserName;
            $this->ToUserName = $this->postObj->ToUserName;
            $this->MsgType = $this->postObj->MsgType;
            $this->Event = $this->postObj->Event;
            $this->EventKey = $this->postObj->EventKey;
            $this->keyword = trim($this->postObj->Content);
            //根据用户OPENID, 检查是否已关联聊球用户, 记录微信用户表
            //查聊球数据库, 看是否已存在此用户
            //if(已存在 则不写) {
                //xxxxx
                //xxxxx
            //} else {
                //不存在, 则获取用户微信信息, 然后记录微信用户表;
                //$userinfo = $this->getInfo();
                //记录微信用户表
            //}
            switch ($this->MsgType)
            {
                case "event":
                    $resultStr = $this->receiveEvent();
                    break;
                case "text":
                    $resultStr = $this->receiveText();
                    break;
                case "image":
                    $resultStr = $this->receiveImage();
                    break;
                case "location":
                    $resultStr = $this->receiveLocation();
                    break;
                case "voice":
                    $resultStr = $this->receiveVoice();
                    break;
                case "video":
                    $resultStr = $this->receiveVideo();
                    break;
                case "shortvideo":
                    $resultStr = $this->receiveShortVideo();
                    break;
                case "link":
                    $resultStr = $this->receiveLink();
                    break;
                default:
                    $contentStr = "抱歉，无法识别你发送的内容".$this->MsgType ;
                    $resultStr = $this->callbackText($contentStr);
                    break;
            }
        } else {
            $resultStr = $this->callbackText($this->str_order);
        }
        echo $resultStr;
        exit;
    }

    public function receiveText() {
        if(empty($this->keyword)){
            $resultStr = $this->callbackText($this->str_order);
        } else {
            switch($this->keyword){
                case "1":
                    $resultStr = $this->getActivity();
                    break;
                case "2":
					$content2 = "聊球app下载链接：\nhttp://app.5usport.com";
					$resultStr = $this->callbackText($content2);
                    break;
				case "3":
                    //
                    break;
				case "4":
					$arr_music = array(
						array("title"=>"越难越爱", "description"=>"美女波波最爱1", "musicUrl"=>"http://sc1.111ttt.com/2014/1/09/27/2272243104.mp3", "HQMusicUrl"=>"http://sc1.111ttt.com/2014/1/09/27/2272243104.mp3"),
						array("title"=>"放生", "description"=>"美女波波最爱2", "musicUrl"=>"http://sc.111ttt.com/up/mp3/91517/44595075D0B889E2D3E8C91F4A9222AA.mp3", "HQMusicUrl"=>"http://sc.111ttt.com/up/mp3/91517/44595075D0B889E2D3E8C91F4A9222AA.mp3"),
						array("title"=>"休止符", "description"=>"美女波波最爱3", "musicUrl"=>"http://sc.111ttt.com/up/mp3/329022/BFF22C333095429199C93546BC945E97.mp3", "HQMusicUrl"=>"http://sc.111ttt.com/up/mp3/329022/BFF22C333095429199C93546BC945E97.mp3"),
						array("title"=>"天使也一样", "description"=>"美女波波最爱4", "musicUrl"=>"http://sc.111ttt.com/up/mp3/79917/E0BE734424C8066C55D2D9E869EF834E.mp3", "HQMusicUrl"=>"http://sc.111ttt.com/up/mp3/79917/E0BE734424C8066C55D2D9E869EF834E.mp3"),
					);
					$resultStr = $this->callbackMusic($arr_music);
                    break;
                case "5":
					$resultStr = $this->callbackText($this->str_help);
                    break;
                case "wx":
                    $userinfo = $this->getUserInfo(1);
                    $resultStr = $this->callbackText($userinfo);
                    break;
                default:
                    $resultStr = $this->callbackText($this->str_order);
                    break;
            }
            return $resultStr;
        }
    }

    //处理事件消息
    private function receiveEvent(){
          $content='';
          switch($this->Event){
              case 'subscribe':
                  $content.="欢迎关注《聊球》公众号，每日主流赛事和热门话题精彩纷呈，美女主持和专业嘉宾准时恭候，高清语音同步直播期待你的互动!";
                  break;
              case 'unsubscribe':
                  $content.="感谢您一直以来对【聊球】公众号的关注，再见!";
                  break;
              //case 'click':
              //    break;
              case 'pic_sysphoto':
                  $content.="收到您的视频/照片了!";
                  break;
              default:
                  $this->callbackEventKey();
                  break;
          }
          return $this->callbackText($content);
    }

    public function receiveImage() {
        $contentStr = "图片发送成功, 需要补充说明文字才能展示在广场．\n\n<a href='". $this->str_domain . "'>点击添加说明文字</a>";
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function receiveLocation() {
        $contentStr = $this->str_order;
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function receiveVoice() {
        $contentStr = $this->str_order;
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function receiveVideo() {
        $contentStr = "小视频发送成功, 需要补充说明文字才能展示在广场．\n\n<a href='". $this->str_domain . "'>点击添加说明文字</a>";
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function receiveShortVideo() {
        $contentStr = "小视频发送成功, 需要补充说明文字才能展示在广场．\n\n<a href='". $this->str_domain . "'>点击添加说明文字</a>";
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function receiveLink() {
        $contentStr = $this->str_order;
        $resultStr = $this->callbackText($contentStr);
        echo $resultStr;
        exit;
    }

    public function callbackEventKey() {
        switch($this->EventKey){
            case "MY_MATCH_SCORE":
                $contentStr = "当前可预测比分的比赛有\n<a href='".$this->str_domain."'>曼城 VS 切尔西</a>\n<a href='".$this->str_domain."'>广州恒大 VS 上海申花</a>";
                $resultStr = $this->callbackText($contentStr);
                break;
            case "MY_INFO":
                $userinfo = $this->getUserInfo(1);
                $resultStr = $this->callbackText($userinfo);
                break;
            case "MY_ACTIVITY":
                $resultStr = $this->getActivity();
                break;
            case "MY_GET_BONUS":
                //这里需要根据FromUserName来读取数据中是否有该用户的红包可以领取
                //查数据表，有则发红包，并提示发送红包成功，请留意微信客户端提示。
                $resultStr = $this->callbackText("你有以下红包可以领取！\n1.首次登录聊球: 已领取\n2.首次发送微博: 已领取\n3.首次分享视频: 已领取\n4.看完一个节目: <a href='http://www.5ulq.com'>点击领取</a>\n5.评论被人推荐: <a href='http://www.5ulq.com'>点击领取</a>\n6.邀请好友注册: <a href='http://www.5ulq.com'>点击领取</a>\n7.完善所有资料: <a href='http://www.5ulq.com'>点击领取</a>");
                break;
            default:
                $contentStr = "美女波波温馨提示；".http_build_query($this->postObj);
                $resultStr = $this->callbackText($contentStr);
                break;
        }
        echo $resultStr;
        exit;
    }

	public function callbackNews($arr_news) {
		$count=count($arr_news);
		$header = "
		<xml>
		<ToUserName><![CDATA[".$this->FromUserName."]]></ToUserName>
		<FromUserName><![CDATA[".$this->ToUserName."]]></FromUserName>
		<CreateTime>".time()."</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>".$count."</ArticleCount>
		<Articles>";
		foreach($arr_news as $value){
            //这里调试用的 如果是李波的微信号,则返回原始数据
            /*
            ToUserName=gh_fd618313fae0&FromUserName=oidcSwjUmM1P2RNXaCURP4pW-Cg0&CreateTime=1439704418&MsgType=text&Content=%E5%95%8A&MsgId=6183483391427505748
            */
            if($this->FromUserName == $this->debug_wx_id){
                $value['description'] .= http_build_query($this->postObj);
            }
			$tmp="
			<item>
			<Title><![CDATA[".$value['title']."]]></Title>
			<Description><![CDATA[".$value['description']."]]></Description>
			<PicUrl><![CDATA[".$value['picurl']."]]></PicUrl>
			<Url><![CDATA[".$value['url']."]]></Url>
			</item>";
			$content .= $tmp;
		}
		$footer="
		</Articles>
		<FuncFlag>1</FuncFlag>
		</xml>";
		return $header.$content.$footer;
	}

	public function callbackText($content) {
		$time = time();
        //这里调试用的 如果是李波的微信号,则返回原始数据
        /*
        ToUserName=gh_fd618313fae0&FromUserName=oidcSwjUmM1P2RNXaCURP4pW-Cg0&CreateTime=1439704418&MsgType=text&Content=%E5%95%8A&MsgId=6183483391427505748
        */
        if($this->FromUserName == $this->debug_wx_id){
            $content .= http_build_query($this->postObj);
        }
		return sprintf($this->textTpl, $this->FromUserName, $this->ToUserName, $time, $content);
	}

    public function callbackMusic($arr_music) {
        $time = time();
        $music_num = count($arr_music)-1;
        $rand_tmp = rand(0, $music_num);
        $title = $arr_music[$rand_tmp]["title"];
        $description = $arr_music[$rand_tmp]["description"];
        $musicUrl = $arr_music[$rand_tmp]["musicUrl"];
        $HQMusicUrl = $arr_music[$rand_tmp]["HQMusicUrl"];
        //这里调试用的 如果是李波的微信号,则返回原始数据
        /*
        ToUserName=gh_fd618313fae0&FromUserName=oidcSwjUmM1P2RNXaCURP4pW-Cg0&CreateTime=1439704418&MsgType=text&Content=%E5%95%8A&MsgId=6183483391427505748
        */
        if($this->FromUserName == $this->debug_wx_id){
            $description .= http_build_query($this->postObj);
        }
        $resultStr = sprintf($this->mscTpl, $this->FromUserName, $this->ToUserName, $time, $title, $description, $musicUrl, $HQMusicUrl);
    }

    //获取用户微信信息
    public function getUserInfo($flag="1") {
        $token = $this->get_access_token();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$this->FromUserName."&lang=zh_CN");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $arr_userinfo = json_decode($result, true);
        /*
        subscribe	用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
        openid	用户的标识，对当前公众号唯一
        nickname	用户的昵称
        sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        city	用户所在城市
        country	用户所在国家
        province	用户所在省份
        language	用户的语言，简体中文为zh_CN
        headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
        subscribe_time	用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
        unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
        remark	公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
        groupid	用户所在的分组ID
        */
        $userinfo = "您的个人资料如下:";
        $userinfo .= "\n昵称：" . $arr_userinfo["nickname"];
        $userinfo .= "\n是否已关注聊球：" . $this->arr_t_f[$arr_userinfo["subscribe"]];
        $userinfo .= "\n性别：" . $this->arr_m_f[$arr_userinfo["sex"]];
        $userinfo .= "\n国家：" . $arr_userinfo["country"];
        $userinfo .= "\n省份：" . $arr_userinfo["province"];
        $userinfo .= "\n城市：" . $arr_userinfo["city"];
        $userinfo .= "\n语言：" . $arr_userinfo["language"];
        $userinfo .= "\n头像：<a href='" . $arr_userinfo["headimgurl"] . "'>点击查看</a>";
        $userinfo .= "\n关注时间：" . date("Y-m-d H:i:s", $arr_userinfo["subscribe_time"]);
        $userinfo .= "\nunionid：" . $arr_userinfo["unionid"];
        $userinfo .= "\n备注：" . $arr_userinfo["remark"];
        //$userinfo .= "\n分组ID：" . $arr_userinfo["groupid"];
        if($flag=="1"){
            return $userinfo;
        } else {
            return $arr_userinfo;
        }
    }

    //获取最新活动
    public function getActivity() {
        $arr_news = array();
        //以下数组是从数据库拿的
        $arr_news = array(
            array(
                "title" => "神秘APP提供全国唯一免费英超揭幕战直播！",
                "picurl" => "http://mmbiz.qpic.cn/mmbiz/trhCj4LFhfzHAHxLQsHSvlYJCVicrQBpL9hWf8ibvOibsSWnmjVwroHBdK8foNibgMw9mnzKF0ovCHBjPGjk3qDicDQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1",
                "desription" => "2015年8月8日，双八交错，必有大事发生。英超揭幕战曼联vs热刺，国内无免费直播，无数球迷哭着跪着拉着“三德子”的脚求直播!你们别闹了好吗!“三德子”表示：臣妾我办不到啊!",
                "url" => "http://mp.weixin.qq.com/s?__biz=MzI1NjAxNDcyOQ==&mid=210640650&idx=1&sn=8c2dde33cca5260682344809deb7f5e2&scene=5#rd"
            ),
            array(
                "title" => "大U送福利：直招穆帅，赢JT签名T恤！",
                "picurl" => "http://img5.5usport.com/data/attachment/forum/201508/08/185241jk5yd46wadk4kayp.jpg",
                "desription" => "赛季首轮，英超卫冕冠军切尔西意外丢分，本周日即将迎来和曼城的重头戏，状态成疑的战车险阻重重。5U体育联合中国切尔西官方球迷会ChinaBlues，发起#如何组装最强战车#微博话题。希望蓝军迷们一起来给穆帅支招，从球队引援、人员搭配到战术阵型，怎样调整才能组装最强战车？",
                "url" => "http://mp.weixin.qq.com/s?__biz=MzAxMDE2OTA5OQ==&mid=208387273&idx=1&sn=1ab7048531e768e42c190e3cc44443b5&scene=1&key=dffc561732c226511e49053c726376c404b21de49e0060ce5a91f1b6c8ca844e8b8a2a63a8b1d57552b2e6efbfea0620&ascene=1&uin=ODE3Mjc5MTAw&devicetype=Windows+7&version=61020020&pass_ticket=4y50DvRbvFkhRyP%2BNBvfHyeXz%2BLivDb60cVP6ftFHGqwnxFtXTDOzWiM7nNnRWDp"
            ),
            array(
                "title" => "英超球迷装逼指南 震惊你的朋友圈！",
                "picurl" => "http://mmbiz.qpic.cn/mmbiz/0sicicziaTwfs5nRZTmFr28FJbxDQNuL0Wiah7tIfwnbrKC0SOTIWJJLkzaUWJ5FSoEeBN33jic1ib3bQgymy0pJLJhw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1",
                "desription" => "现在只要说我是英超球迷就体现了你的逼格(版权贵看球收费啊！)所以在8月不看英超，谁和你玩耍？小编为保障各档次球迷之间的和(Zhuang)谐(bi)，呕心沥血总结了英超球迷装逼宝典，推出各类装逼套装，读完此文，保证屌丝球迷一秒钟变高富帅，震惊你的朋友圈！",
                "url" => "http://mp.weixin.qq.com/s?__biz=MzAxMDE2OTA5OQ==&mid=208387273&idx=2&sn=e750a1d5044d26edb1e8a9468f5b899e&scene=5#rd"
            ),
            array(
                "title" => "如果你有朋友是阿森纳球迷，请一定要珍惜他！",
                "picurl" => "http://mmbiz.qpic.cn/mmbiz/cW3BPIsT2tpJpTHtiaiaTkI8MZD56jnmBMVnF8Dia1qBgT7DB4pvm5RrEdlTOAwBNkXb9SsQIZ7089Eh0SRGq4YZg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1",
                "desription" => "所以如果你的朋友是阿森纳球迷，与其见面时，请不要和他提英超第一轮，吃饭聚餐就不要和他AA制了，你请他吧。有时间多陪陪他，约他聊天，逛街时你来买买单都是不错的选择。理解他，包容他。若最近他想去楼顶、河边、大桥等地方，请紧密陪伴。生命来之不易，生活需要互相扶持！",
                "url" => "http://mp.weixin.qq.com/s?__biz=MzA4MzcyMjUxNw==&mid=212116481&idx=1&sn=58386ffa755e48104d87d9c4e2062fe1&scene=5#rd"
            ),
        );
        return $this->callbackNews($arr_news);
    }

    //创建自定义菜单
    public function createMenu(){
        /*
        $arr = array(
            'button' =>array(
                array(
                    'name'=>urlencode("直播室"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("每日精选"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("随便听听"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("节目列表"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        )
                    )
                ),
                array(
                    'name'=>urlencode("小视频"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("猜你喜欢"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("视频头条"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("我的视频"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("拍小视频"),
                            'type'=>'pic_sysphoto',
                            'key'=>'VEO_CAMERA'
                        )
                    )
                ),
                array(
                    'name'=>urlencode("我的"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("下载APP"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("领取红包"),
                            'type'=>'click',
                            'key'=>"MY_GET_BONUS"
                        ),
                        array(
                            'name'=>urlencode("最新活动"),
                            'type'=>'click',
                            'key'=>"MY_ACTIVITY"
                        ),
                        array(
                            'name'=>urlencode("球迷联盟"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("个人中心"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        )
                    )
                )
            )
        );
        */
        /*
		$arr = array(
            'button' =>array(
                array(
                    'name'=>urlencode("节目秀"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("拍小视频"),
                            'type'=>'pic_sysphoto',
                            'key'=>'VEO_CAMERA'
                        ),
                        array(
                            'name'=>urlencode("订制节目"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("随便听听"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                        array(
                            'name'=>urlencode("聊球热点"),
                            'type'=>'view',
                            'url'=>$this->str_domain
                        ),
                    )
                ),
                array(
                    'name'=>urlencode("领红包"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("领取红包"),
                            'type'=>'click',
                            'key'=>"MY_GET_BONUS"
                        ),
                        array(
                            'name'=>urlencode("最新活动"),
                            'type'=>'click',
                            'key'=>"MY_ACTIVITY"
                        ),
                    )
                ),
                array(
                    'name'=>urlencode("下载APP"),
                    'type'=>'view',
                    'url'=>$this->str_domain
                )
            )
        );
		*/
		
		$arr = array(
            'button' =>array(
                array(
                    'name'=>urlencode("下载APP"),
                    'type'=>'view',
                    'url'=>"http://app.5usport.com"
                )
            )
        );
        $data = urldecode(json_encode($arr));
        $token = $this->get_access_token();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        return $result;
    }

    //查询自定义菜单
    public function getMenu(){
        $token = $this->get_access_token();
        $url="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=$token";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true) ; //获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true) ; //在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        return $output = curl_exec($ch);
    }

    //删除自定义菜单
    public function deleteMenu(){
        $token = $this->get_access_token();
        $url="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
    * 获取access_token
    */
    private function get_access_token()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->app_id."&secret=".$this->app_secret;
        $data = json_decode(file_get_contents($url),true);
        if($data['access_token']){
            return $data['access_token'];
        }else{
            return "获取access_token错误";
        }
    }

    //签名验证函数
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}