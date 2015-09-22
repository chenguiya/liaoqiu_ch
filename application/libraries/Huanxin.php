<?php
/* *************************************************************************************
 * 环信聊天SDK处理类
 * 版本：2.1.3
 * 日期：2015-9-18
 * 创建：libo
 * 说明：聊球
 * 提示：如何获取API文档方法（http://www.easemob.com/rest/）
 ***************************************************************************************/

// +-------------------------------------------------------------+
// | Easemob Demo for PHP Version 2.1.3                          |
// | 该版本由5usport.com的技术人员根据上一版本小李飞刀整理的            |
// | 基础上优化的环信PHP版本DEMO，版本号: 2.1.3　　　　　　　　　　　　　 |
// +-------------------------------------------------------------+
// | Copyright (c) 2014 5U体育  www.5usport.com                   |
// +-------------------------------------------------------------+
// | Licensed ( http://www.5usport.com/LICENSE )                 |
// +-------------------------------------------------------------+
// | Author: libo <libo@5usport.com>                             |
// | 感谢小李飞刀<limx@xiaoneimimi.com>整理出来的旧版本，站在巨人肩膀上|
// +-------------------------------------------------------------+
/**
 * 环信-服务器端REST API
 * @author    libo <libo@5usport.com>
 */
class Huanxin {
	private $client_id;
	private $client_secret;
	private $org_name;
	private $app_name;
	private $token_dir;
	private $uploads_images_dir;
	private $url;

	/**
	 * 初始化参数
	 *
	 * @param array $options
	 * @param $options['client_id']
	 * @param $options['client_secret']
	 * @param $options['org_name']
	 * @param $options['app_name']
	 */
	public function __construct() {
	    $_CI = &get_instance();
		$_CI->config->load('huanxin_config');
		
		$client_id = $_CI->config->item('client_id');
		$client_secret = $_CI->config->item('client_secret');
		$org_name = $_CI->config->item('org_name');
		$app_name = $_CI->config->item('app_name');
		$token_dir = $_CI->config->item('token_dir');
		$uploads_images_dir = $_CI->config->item('uploads_images_dir');
		
		$this->client_id = !empty ($client_id) ? $client_id : '';
		$this->client_secret = isset ($client_secret) ? $client_secret : '';
		$this->org_name = isset ($org_name) ? $org_name : '';
		$this->app_name = isset ($app_name) ? $app_name : '';
		$this->token_dir = isset ($token_dir) ? $token_dir : '';
		$this->uploads_images_dir = isset ($uploads_images_dir) ? $uploads_images_dir : '';
		if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
			$this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
		}
	}
	/**
	 * 开放注册模式（不建议使用这种模式注册）
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码
	 */
	public function openRegister($options) {
		$url = $this->url . "users";
		$result = $this->postCurl ( $url, $options, $head = array() );
		return $result;
	}

	/**
	 * 获取用户列表[批量] 默认获取最新创建的10个用户
	 * 描述：获取某个app下指定数量的环信账号列表。上述url可一次获取10个用户,数值可以修改 建议这个数值在10-100之间，不要过大
	 *
	 * @param $limit="10" 默认为10条
	 * @param $ql 查询条件
	 *        	如ql=order+by+created+desc 按照创建时间来排序(降序)
	 */
	public function userList($limit="10") {
		$url = $this->url . "users?limit=" . $limit;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'GET' );
		return $result;
	}

	/**
	 * 授权注册模式 || 批量注册
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码
	 *        	批量注册传二维数组
	 */
	public function accreditRegister($options) {
		$url = $this->url . "users";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $options, $header );
		return $result;
	}

	/**
	 * 获取指定用户详情
	 *
	 * @param $username 用户名
	 */
	public function userDetails($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'GET' );
		return $result;
	}

	/**
	 * 重置用户密码
	 *
	 * @param $options['username'] 用户名
	 * @param $options['password'] 密码    //这个参数不需要
	 * @param $options['newpassword'] 新密码
	 */
	public function resetPassword($options) {
		$url = $this->url . "users/" . $options ['username'] . "/password";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$params = array();
		$params["newpassword"] = $options['newpassword'];
		$result = $this->postCurl ( $url, $params, $header, $type = 'PUT' );
		return $result;
	}


	/**
	 * 设置用户昵称
	 *
	 * @param $options['username'] 用户名
	 * @param $options['nickname'] 密码
	 */
	public function setNickname($options) {
		$url = $this->url . "users/" . $options ['username'] ;
		$params = array();
		$params["nickname"] = $options['nickname'];
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $params, $header, $type = 'PUT' );
		return $result;
	}

	/**
	 * 删除用户
	 *
	 * @param $username 用户名
	 */
	public function deleteUser($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
		return $result;
	}

	/**
	 * 批量删除用户
	 * 描述：删除某个app下指定数量的环信账号。上述url可一次删除300个用户,数值可以修改 建议这个数值在100-500之间，不要过大
	 *
	 * @param $limit="300" 默认为300条
	 * @param $ql 删除条件
	 *        	如ql=order+by+created+desc 按照创建时间来排序(降序)
	 */
	public function batchDeleteUser($limit = "300", $ql = '') {
		$url = $this->url . "users?limit=" . $limit;
		if (! empty ( $ql )) {
			$url = $this->url . "users?ql=" . $ql . "&limit=" . $limit;
		}
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
		return $result;
	}

	/**
	 * 给一个用户添加一个好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function addFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header );
		return $result;
	}

	/**
	 * 删除好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function deleteFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 查看用户的好友
	 *
	 * @param
	 *        	$owner_username
	 */
	public function showFriend($owner_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}

	/**
	 * 拉黑，添加到黑名单
	 *
	 * @param
	 *        	$username
	 * @param
	 *        	$friend_username 数组
	 */
	public function addBlackuser($username, $friend_username) {
		$url = $this->url . "users/" . $username . "/blocks/users";
		$access_token = $this->getToken ();
		$params = array();
		$params["usernames"] = $friend_username;
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $params, $header );
		return $result;
	}

	/**
	 * 从黑名单中移除
	 *
	 * @param
	 *        	$username
	 * @param
	 *        	$friend_username 数组
	 */
	public function delBlackuser($username, $friend_username) {
		$url = $this->url . "users/" . $username . "/blocks/users/" . $friend_username ;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type= "DELETE" );
		return $result;
	}

	/**
	 * 查看用户的黑名单
	 *
	 * @param
	 *        	$owner_username
	 */
	public function showBlacklist($owner_username) {
		$url = $this->url . "users/" . $owner_username . "/blocks/users/";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}

	/**
	 * 查看用户在线状态
	 *
	 * @param
	 *        	$username
	 */
	public function checkUserstat($username) {
		$url = $this->url . "users/" . $username . "/status";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}

	// +----------------------------------------------------------------------
	// | 聊天相关的方法
	// +----------------------------------------------------------------------
	/**
	 * 查看用户是否在线
	 *
	 * @param
	 *        	$username
	 */
	public function isOnline($username) {
		$url = $this->url . "users/" . $username . "/status";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 发送消息
	 *
	 * @param string $from_user
	 *        	发送方用户名
	 * @param array $username
	 						接收方用户组，可1个或多个，但必须要传数组
	 *        	array('1','2')
	 * @param string $target_type
	 *        	默认为：users 描述：给一个或者多个用户(users)或者群组发送消息(chatgroups)
	 * @param string $content
	 						消息主体；
	 * @param array $ext
	 *        	自定义参数
	 */
	function hxSend($from_user = "admin", $username, $params, $target_type = "users", $ext) {
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取app中所有的群组
	 */
	public function chatGroups() {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 创建群组
	 *
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['public'] //是否是公开群,
	 *        	此属性为必须的 true or false
	 * @param $option['approval'] //加入公开群是否需要批准,
	 *        	没有这个属性的话默认是true, 此属性为可选的
	 * @param $option['owner'] //群组的管理员,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的
	 */
	public function createGroup($option) {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}


	/**
	 * 获取群组详情
	 *
	 * @param
	 *        	$group_id
	 */
	public function chatGroupsDetails($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 删除群组
	 *
	 * @param
	 *        	$group_id
	 */
	public function deleteGroup($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 获取群组成员
	 *
	 * @param
	 *        	$group_id
	 */
	public function groupsUser($group_id) {
		$url = $this->url . "chatgroups/" . $group_id . "/users";
		//echo $url;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}

	/**
	 * 修改群组资料
	 *
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的
	 */
	public function modifyGroup($group_id, $option) {
		$url = $this->url. "chatgroups/". $group_id;
		//echo $url;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header, $type = 'PUT' );

		return $result;
	}

	/**
	 * 群组添加成员（添加一个）
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function addGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "POST" );
		return $result;
	}

	/**
	 * 群组添加成员（批量添加）
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$arr_user
	 */
	public function batchaddGroupsUser($group_id, $arr_user) {
		$option ['usernames'] = $arr_user;
		//return json_encode($arr_user);
		$url = $this->url . "chatgroups/" . $group_id . "/users";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}

	/**
	 * 群组删除成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function delGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 聊天消息记录
	 *
	 * @param $ql 查询条件如：$ql
	 *        	= "select+*+where+from='" . $uid . "'+or+to='". $uid ."'+order+by+timestamp+desc&limit=" . $limit . $cursor;
	 *        	默认为order by timestamp desc
	 * @param $cursor 分页参数
	 *        	默认为空
	 * @param $limit 条数
	 *        	默认20
	 */
	public function chatRecord($ql = '', $cursor = '', $limit = 20) {
		$ql = ! empty ( $ql ) ? "ql=" . $ql : "order+by+timestamp+desc";
		$cursor = ! empty ( $cursor ) ? "&cursor=" . $cursor : '';
		$url = $this->url . "chatmessages?" . $ql . "&limit=" . $limit . $cursor;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET " );
		return $result;
	}
	//查询聊天记录 第二种 写法，只带一个参数。所以cursor和limit全写在sql语句里
	public function chatRecord2($ql = '') {
		$ql = ! empty ( $ql ) ? "ql=" . $ql : "order+by+timestamp+desc";
		$url = $this->url . "chatmessages?" . $ql;
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET " );
		return $result;
	}

	/*上传图片到聊球服务器*/
  public function uploadsImage($post_images)
  {
      $img_base64 = base64_decode($post_images);
      $path = $this->uploads_images_dir;
	  if(!is_dir($path)) mkdir($path,777);
      $file_name = substr($_REQUEST["name"], 0, -4)."_".substr(time(), -4).".jpg";
      $no_prefix = substr($file_name, 0, -4);
      $file_path = $path.$file_name;
      //保存图片到服务器
      file_put_contents($file_path, $img_base64);
      //发送环信图片消息
  }

  /*上传图片到环信服务器*/
  public function uploadsHXImage($option) {
  		$option['upload_file'] = 1;
		$url = $this->url . "chatfiles";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$header[]='restrict-access:true';
		$result = $this->postCurl ( $url, $option, $header );
		return $result;  	
  }

  /*
  *下载图片，语音文件
  *
  * $url 下载的HX链接， xxxx/uuid
  * $uuid HX的文件资源IDID
  * $save_path 保存的位置
  * $secret hx提供的secret
  * $token 应用专用的
  * $is_thumb 是否是缩略图
  *
  */
  public function downloadHXFiles($url, $file_name, $save_path, $secret,$is_thumb) {
  	$header = array();
  	$access_token = $this->getToken ();
  	if($is_thumb=="1") {
        array_push($header, 'thumbnail: true');
    }
    array_push($header, 'share-secret: ' . $secret);
    array_push($header, $access_token);
    array_push($header, 'Accept:application/octet-stream');
    $ch = curl_init ();
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	
    curl_setopt ( $ch, CURLOPT_URL, $url );
	if (count($header) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
    ob_start ();
    curl_exec ( $ch );
    $return_content = ob_get_contents ();
    ob_end_clean ();

    //$return_content = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

    $fp= fopen($save_path."/".$file_name, "a"); //将文件绑定到流
    $res = fwrite($fp, $return_content); //写入文件

  	return $res;
  }

	/**
	 * 获取Token
	 */
	//apptoken POST /{org_name}/{app_name}/token
	private function getToken()
	{
		$option ['grant_type'] = "client_credentials";
		$option ['client_id'] = $this->client_id;
		$option ['client_secret'] = $this->client_secret;
		$url = $this->url . "token";
		if(!is_dir($this->token_dir)) mkdir($this->token_dir,0777);
		$token_dir = $this->token_dir ."easemob.txt";
		$file_true = file_exists("./".$token_dir);
		if($file_true) $fp = trim(file_get_contents ($token_dir));
		$fx = 'Authorization: Bearer ';
		if (isset($fp)) {
			$arr = json_decode ($fp, true);
			if(!empty($arr['access_token'])) {
				if ($arr ['expires_in'] < time ()) {
					$result = $this->postCurl($url, $option);
					$result ['expires_in'] = $result ['expires_in'] + time ();
					file_put_contents( $token_dir, json_encode( $result ));
					return $fx.$result ['access_token'];
				}
				return $fx.$arr ['access_token'];
			}
		}

		$res = $this->postCurl($url, $option);

		$result = json_decode($res, true);
		$result ['expires_in'] = $result ['expires_in'] + time ();

		file_put_contents($token_dir, json_encode( $result ));
		return $fx.$result["access_token"];
	}

	/**
	* 提交数据 postCurl
	**/
	private function postCurl ($url, $body, $header = array(), $method = "POST")
	{
		if(is_array($body) && !isset($body['upload_file'])) {//上传图片不需要转格式
			$body = json_encode($body);
		}
		array_push($header, 'Accept:application/json');
		if(isset($body['upload_file'])){  //上传图片
			array_push($header, 'Content-Type:multipart/form-data');
		}else{
			array_push($header, 'Content-Type:application/json');
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		switch ($method){
			case "GET" :
				curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST,true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				//echo "<pre>"; print_R($body);
			break;
			case "PUT" :
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				//echo "<pre>"; print_R($body);
			break;
			case "DELETE":
				curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			break;
		}
		curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		if (isset($body{3}) > 0) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}
		if (count($header) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		
		$ret = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if ($err) {
			return $err;
		}
		return $ret;
	}

	/**
	 * 创建聊天室  by liuzhengyong
	 *
	 * @param $option['name'] //名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['owner'] //群组的管理员,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的
	 */
	public function createRoom($option) {
		$url = $this->url . "chatrooms";
		$access_token = $this->getToken ();
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}

        /**
         * 转让聊天室
         * 
         * @param int $group_id
         * @param array $option
         * @return type
         */
        public function changeGroupOwner($group_id, $option)
        {
            	$url = $this->url. "chatgroups/". $group_id;
		//echo $url;
		$access_token = $this->getToken ();
                $header[] = 'Content-Type:"application/json"';
		$header [] = $access_token;
		$result = $this->postCurl ( $url, $option, $header, $type = 'PUT' );

		return $result;
        }
}