<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 获取用户的登录信息
function get_member_info()
{
    $arr_member = array('user_id' => @$_SESSION['user_id'] + 0,         // CI的member id
                        'user_name' => trim(@$_SESSION['user_name']),	// 用户名
						'nick_name' => trim(@$_SESSION['nick_name']),	// 昵称
                        'avatar_url' => trim(@$_SESSION['avatar_url']), // 头像URL
                        'user_id_uc' => trim(@$_SESSION['user_id_uc']), // ucenter 用户ID
                        'logined' => (@$_SESSION['user_id'] + 0 > 0) ? TRUE : FALSE);
    return $arr_member;
}

/**
 * 获取请求ip
 *
 * @return string ip地址
 */
function ip()
{
    /* 需要考虑
    $ip = $_SERVER['REMOTE_ADDR'];
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    */
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}


/**
 * 字符串截取函数
 *
 * @param   $str        要处理的字符串
 * @param   $start      开始位置
 * @param   $length     长度
 * @param   $charater   字符串编码
 * @param   $ppp        多加的后缀
 * @return  string
 */
function str_intercept($str, $start, $length, $charater='UTF-8', $ppp = "...")
{
    $len = mb_strlen($str, $charater);
    if($start >= $length)
    {
        $return = $str;
    }
    else
    {
        $return = mb_substr($str, $start, $length, $charater);
    }
    if(mb_strlen($return,$charater) < ($len-$start))
        $return .= $ppp;
    return $return;
}

/**
 * 时间戳时间格式化输出
 *
 * @param   $time   时间戳
 * @param   $type   类型 0 => '2014-11-17 12:26:21' | 1 => '2014-11-17' | 2 => '2014年05月23日 16:51'
 * @return  string
 */
function time_format($time, $type = 0)
{
    $time = $time + 0;

    if($type == 0)
        return date('Y-m-d H:i:s', $time);
    if($type == 1)
        return date('Y-m-d', $time);
    if($type == 2)
        return date('Y-m-d H:i', $time);
    if($type == 3)
        return date('Y年m月d日 H:i', $time);
    if($type == 4)
        return date('m月d日 H:i', $time);

}


/**
 *  函数名称:decode
 *  函数作用:加密解密字符串
 *  使用方法:
 *      加密:decode('daichao', 'E', 'daichao');
 *      解密:decode('被加密过的字符串', 'D', 'daichao');
 *  参数说明:
 *      $string   :需要加密解密的字符串
 *      $operation:判断是加密还是解密; E:加密 D:解密 $key:加密的钥匙(密匙);
 *  注:此函数加密为固定,非随机加密,也就是说同一字符串加密后刷新也不会变
 */
function decode($string, $operation, $key = '')
{
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string.$key),0,8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($key[$i%$key_length]);
        $box[$i] = $i;
    }

    for($j = $i = 0; $i < 256; $i++)
    {
       $j = ($j + $box[$i] + $rndkey[$i])%256;
       $tmp = $box[$i];
       $box[$i] = $box[$j];
       $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a+1)%256;
        $j = ($j+$box[$a])%256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }

    if($operation == 'D')
    {
        if(substr($result,0,8) == substr(md5(substr($result,8).$key),0,8))
            return substr($result,8);
        else
            return '';
    }
    else
    {
        return str_replace('=','',base64_encode($result));
    }
}


/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password)
{
    // 密码长度8~16位，数字、字母、字符至少包含两种
    $strlen = strlen($password);
    if($strlen >= 8 && $strlen <= 16)
    {
        $arr_hit = array(0, 0, 0);
        $arr_str = array('0123456789', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ' ~!@#$%^&*()_+`-={}[]|\?/<>,.;:"'."'");

        for($i = 0; $i< $strlen; $i++)
        {
            $char = chr(ord($password[$i]));

            $r0 = strpos($arr_str[0], $char);
            $r1 = strpos($arr_str[1], $char);
            $r2 = strpos($arr_str[2], $char);

            if($r0 !== FALSE) $arr_hit[0] = 1;
            if($r1 !== FALSE) $arr_hit[1] = 1;
            if($r2 !== FALSE) $arr_hit[2] = 1;
        }

        return ($arr_hit[0] + $arr_hit[1] + $arr_hit[2] >= 2) ? TRUE : FALSE;
    }
    else
    {
        return FALSE;
    }
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
    $strlen = strlen($username);
    if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][@\.a-zA-Z0-9_\x7f-\xff]+$/", $username)){
        return false;
    } elseif ( 32 < $strlen || $strlen < 2 ) {
        return false;
    }
    return true;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 判断日期格式是否正确
 */
function is_date($date) {
    return preg_match("/^[0-9]{4}(\-|\/)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/", $date);
}


/**
 * 判断mobile格式是否正确
 * @param $mobile
 */
function is_mobile($mobile) {
    if(preg_match("/1[3458]{1}\d{9}$/", $mobile))
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

 /**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
    $badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
    foreach($badwords as $value){
        if(strpos($string, $value) !== FALSE) {
            return TRUE;
        }
    }
    return FALSE;
}




// 写文件日志记录 用在ERROR记录，目录一定是cache下,file_path相对路径
function write_file_log($content, $file_path = '', $file_head = 'log_error', $send_mail = TRUE, $token = '')
{
    $catch_dir = './upload/cache';
    if(!is_dir($catch_dir)) mkdir($catch_dir,777);
    if($file_path == '')
    {
        $file_path = $catch_dir;
    }
    else
    {
        $_dir = '';
        if(!preg_match("/^[a-z\d\/_-]*$/i",$file_path)) 
        {
            die('路径有特殊字符');
        }
        
        $arr_path = explode(DS, $file_path);
        for($i = 0; $i < count($arr_path); $i++)
        {
            if(trim($arr_path[$i]) != '')
            {
                $_dir = '';
                for($j = 0; $j < count($arr_path); $j++)
                {
                    if($j <= $i && trim($arr_path[$j]) != '' && trim($arr_path[$i]) != '')
                    {
                        $_dir .= $arr_path[$j].DS;
                    }
                }
                
                if(trim($_dir) != '')
                {
                    $_dir = $catch_dir.$_dir;
                    if(!is_dir($_dir)) @mkdir($_dir, 0777);
                }
                else
                {
                    die('路径为空');
                }
            }
        }
        
        if(trim($_dir) != '')
        {
            $file_path = $_dir;
        }
        else
        {
            die('路径为空2');
        }
    }
    
    if($token != '')
    {
        $file_name = $token.'.txt';
    }
    else
    {
        $file_name = $file_head.'_'.date('Ymd').'.txt';
    }
    
    $ip = ip();
    $data = date('Y-m-d H:i:s');

    if($file_head == 'log_error')
    {
        // $add_info = 'SERVERID:'.@$_COOKIE['SERVERID']."|";
        $add_info = "SERVER_ADDR:".@$_SERVER['SERVER_ADDR']."|";
        $add_info .= "SERVER_NAME:".@$_SERVER['SERVER_NAME']."|";
        $add_info .= 'URL:http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."\n";
        $content = $add_info.$content;
        $content .= "\n=================================================================================";

        if($send_mail)  // 默认发Email
        {
            // m1 zhangjh 2015-02-02 有错发Email
            $_CI =& get_instance();
            $_CI->load->library('email');
            $_CI->email->from($_CI->email->smtp_user, '5UError');

            if(@$_SERVER['SERVER_ADDR'] == '192.168.11.3' || @$_SERVER['SERVER_ADDR'] == '192.168.11.4' || @$_SERVER['SERVER_ADDR'] == '192.168.11.5')
            {
                //$_CI->email->to('apple@5usport.com, luojing@5usport.com, mxh@5usport.com, yyh@5usport.com, zhangjianhua@5usport.com, zzf@5usport.com, libo@5usport.com');
				$_CI->email->to('zhangjianhua@5usport.com');
            }
            else
            {
                $_CI->email->to('zhangjianhua@5usport.com');
            }
            
            $_CI->email->subject(@$_SERVER['SERVER_NAME']." Error");
            $_CI->email->message("[".$data."]<br>".str_replace(array("|", "\n"), "<br>", 'IP:'.$ip.'|'.$content).
                                 "<br>出现以上错误，请相关技术及时处理".
                                 "<br>发送这封Email用的是application/helpers/common_helper.php中的write_file_log");
            if(!$_CI->email->send())
            {
                //$email_send_result = '发送Email失败';
            }
            else
            {
                //$email_send_result = '发送Email成功';
            }
        }
    }
    @error_log('['.$data.'] IP:'.$ip.'|'.$content."\n", 3, $file_path.$file_name);
}

// 读文件日志记录
function watch_file_log($file_path = '', $file_head = 'log_watch', $show_line = 100, $date = '', $token = '')
{
    $catch_dir = dirname(__FILE__).DS.'..'.DS.'..'.DS.'upload/cache'.DS;
    
    if($file_path == '')
    {
        $file_path = $catch_dir;
    }
    else
    {
        $_dir = '';
        if(!preg_match("/^[a-z\d\/_-]*$/i",$file_path)) 
        {
            die('路径有特殊字符');
        }
        
        $arr_path = explode(DS, $file_path);
        for($i = 0; $i < count($arr_path); $i++)
        {
            if(trim($arr_path[$i]) != '')
            {
                $_dir = '';
                for($j = 0; $j < count($arr_path); $j++)
                {
                    if($j <= $i && trim($arr_path[$j]) != '' && trim($arr_path[$i]) != '')
                    {
                        $_dir .= $arr_path[$j].DS;
                    }
                }
                
                if(trim($_dir) != '')
                {
                    $_dir = $catch_dir.$_dir;
                }
                else
                {
                    die('路径为空');
                }
            }
        }
        
        if(trim($_dir) != '')
        {
            $file_path = $_dir;
        }
        else
        {
            die('路径为空2');
        }
    }
    
    if($token != '')
    {
        $file_name = $token.'.txt';
    }
    else
    {
        if($date == '')
        {
            $file_name = $file_head.'_'.date('Ymd').'.txt';             // 只能看当天的log
        }
        else
        {
            $file_name = $file_head.'_'.$date.'.txt';
        }
    }
    
    $lines = file($file_path.$file_name);

    $lines_out = array();
    if($file_head == 'log_watch') // 如果是查看日志的，倒叙输出
    {
        $j = 0;
        for($i = count($lines); $i >= 0; $i--)
        {
            $lines_out[$j] = @$lines[$i];
            if($j >= $show_line)
            {
                break;
            }
            $j++;
        }

        foreach ($lines_out as $line_num => $line)
        {
            echo htmlspecialchars($line);
        }
    }
    elseif($file_head == 'log_error')   // 查看错误日志分组倒叙
    {
        $lines_out = array();
        $arr_group = array();

        $j = 0;
        $is_open = 0;

        for($i = 0; $i < count($lines); $i++)
        {
            // echo $i." => ".$lines[$i];
            // 查找是否含有形如 [2015-01-27 10:17:07] 的行
            $have_date = preg_match("/^\[[0-9]{4}(\-|\/)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))\](.*)$/", $lines[$i]);

            // 查找是否含有形如 ============ 的行
            $have_line = preg_match("/^============(.*)$/", $lines[$i]);

            $arr_group[$j] = @$arr_group[$j] == '' ? '' : $arr_group[$j];
            if($have_date)
            {
                $is_open = 1;
                $arr_group[$j] .= $lines[$i];
            }
            elseif($is_open == 1)
            {
                $arr_group[$j] .= $lines[$i];
                if($have_line)
                {
                    $j++;
                    $is_open = 0;
                }
            }else
            {
            }
        }

        $j = 0;
        for($i = count($arr_group); $i >= 0; $i--)
        {
            $lines_out[$j] = @$arr_group[$i];
            if($j >= $show_line)
            {
                break;
            }
            $j++;
        }

        foreach ($lines_out as $line_num => $line)
        {
            echo htmlspecialchars($line);
        }
    }
}



function alert($msg=NULL,$url=NULL,$go=false){
		if($msg) echo "<script>alert('".$msg."');</script>";
		if($url) echo "<script>window.location.href='/admin/{$url}';</script>";
		if($go) echo "<script>history.go(-1);</script>";
		die;
}

function get_urldata($url,$return='array'){	
		$data = file_get_contents($url);
		if($return=='array'){
			$data = json_decode($data,true);
			$data  =  $data['content'];
		}
		return $data;
}


//输出json数组给APP手机
function output($ret) {
    
    $ret = inttostr($ret);
	die(json_encode($ret));
}

function inttostr($ret) {
    if(!empty($ret))
    {
        foreach ($ret as $k => $v) {
            //Do something here.
            if(is_array($v)){
                $ret[$k] = inttostr($v);
            } else if(is_int($v)) {
                $ret[$k] = $v."";
            }
        }
    }
    
    return $ret;
}


/**
 *  简化抛出异常
 * 
 * @param string $message
 * @param int $code
 * @param object $previous
 * @throws Exception
 */
function  _E($message, $code, $previous = null)
{
    throw new Exception($message, $code, $previous);
}

/**
 * 验证令牌
 * 
 * @param string $token
 */
function check_token($token = null)
{
    if(!isset($token) || empty($token))
    {
        return false;
    }
    
    if(strlen($token) != 32)
    {
        return false;
    }
    
    return true;
}

/**
 * 验证NICKNAME是否合法
 * 
 * @param string $nickname
 */
function check_nickname($nickname = '')
{
    if(empty($nickname))
    {
        return false;
    }
    
    return is_username($nickname) ? true : false;
    //return true;
}

function isEmpty($value){
		if(empty($value)){  //如果为空，直接输出没有信息
				$arr_return = array('state_code'=>0,'state_desc'=>'没有信息');
				output($arr_return);
		}
}

function Getzimu($str)
{
    $str= iconv("UTF-8","gb2312", $str);//如果程序是gbk的，此行就要注释掉
    if (preg_match("/^[\x7f-\xff]/", $str))
    {
        $fchar=ord($str{0});
        if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0});
        $a = $str;
        $val=ord($a{0})*256+ord($a{1})-65536;
        if($val>=-20319 and $val<=-20284)return "A";
        if($val>=-20283 and $val<=-19776)return "B";
        if($val>=-19775 and $val<=-19219)return "C";
        if($val>=-19218 and $val<=-18711)return "D";
        if($val>=-18710 and $val<=-18527)return "E";
        if($val>=-18526 and $val<=-18240)return "F";
        if($val>=-18239 and $val<=-17923)return "G";
        if($val>=-17922 and $val<=-17418)return "H";
        if($val>=-17417 and $val<=-16475)return "J";
        if($val>=-16474 and $val<=-16213)return "K";
        if($val>=-16212 and $val<=-15641)return "L";
        if($val>=-15640 and $val<=-15166)return "M";
        if($val>=-15165 and $val<=-14923)return "N";
        if($val>=-14922 and $val<=-14915)return "O";
        if($val>=-14914 and $val<=-14631)return "P";
        if($val>=-14630 and $val<=-14150)return "Q";
        if($val>=-14149 and $val<=-14091)return "R";
        if($val>=-14090 and $val<=-13319)return "S";
        if($val>=-13318 and $val<=-12839)return "T";
        if($val>=-12838 and $val<=-12557)return "W";
        if($val>=-12556 and $val<=-11848)return "X";
        if($val>=-11847 and $val<=-11056)return "Y";
        if($val>=-11055 and $val<=-10247)return "Z";
    }
    elseif (preg_match("/^[a-zA-Z]/", $str))
    {
        $fchar=ord($str{0});
        if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0});
        $a = $str;
        return $a;
    }
    elseif (preg_match("/^[0-9]/", $str))
    {
        $str_1st = substr($str, 0, 1);
        $str_1st_new = "";
        switch($str_1st){
            case "0":
                $str_1st_new = "零";
                break;
            case "1":
                $str_1st_new = "一";
                break;
            case "2":
                $str_1st_new = "二";
                break;
            case "3":
                $str_1st_new = "三";
                break;
            case "4":
                $str_1st_new = "四";
                break;
            case "5":
                $str_1st_new = "五";
                break;
            case "6":
                $str_1st_new = "六";
                break;
            case "7":
                $str_1st_new = "七";
                break;
            case "8":
                $str_1st_new = "八";
                break;
            case "9":
                $str_1st_new = "九";
                break;
        }
        return Getzimu($str_1st_new);
    }
    else
    {
        return "#";
    }
}

function write_logs($type, $token, $title, $str_logs) {
	if($type == 1) {
		$logs_path = "liaogeqiu/user_logs/".date("Y-m-d");
	} elseif($type == 2) {
		$logs_path = "liaogeqiu/phpcms_logs/".date("Y-m-d");
	}
	if(!is_dir($logs_path)) {
	  mkdir($logs_path, 0777, true);
	}
    if(is_array($str_logs)){
        $str_logs = var_export($str_logs, true);
    }
	//$content = "=============[ ".$title." ]==[ ".date("Y-m-d H:i:s")." ]=============\r\n".$str_logs."\r\n\r\n\r\n";
	$file_path = $logs_path."/".$token.".log";
	//echo $file_path;
	//error_log($content, 3, $file_path);
   // write_file_log($str_logs, $logs_path, 'log_error', FALSE, $token);
}

function show_logs($type, $token, $date) {
    //echo 'Curl: ', function_exists('curl_version') ? 'Enabled' : 'Disabled' . '<br />file_get_contents: ', file_get_contents(__FILE__) ? 'Enabled' : 'Disabled';
    if($type == 1) {
		$logs_path = "liaogeqiu/user_logs/".$date;
	} elseif($type == 2) {
		$logs_path = "liaogeqiu/phpcms_logs/".$date;
	}
    //$str_logs = file_get_contents($logs_path);
    return watch_file_log($logs_path, 'log_error', 1000, '', $token);
}

function object_array($object) {
   $object = json_decode(json_encode($object), true);
   return $object;
}

function is_num($num) {
	if(preg_match("/^\d+$/", $num)) {
		return true;
	} else {
		return false;
	}
}

function is_time($data) {
	$is_date = strtotime($data) ? strtotime($data):false;
	if($is_date===false){
	    return false;
	}else{
			return true;
	    //echo date('Y-m-d',$is_date);//只要提交的是合法的日期，这里都统一成2014-11-11格式
	}
}

/**
 * 检查EMAIL地址是否合法
 * 
 * @param type $email
 */
function check_email($email = '')
{
    if(!isset($email) || empty($email))
    {
        return falfse;
    }
    
    $email_patern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
    return preg_match($email_patern, $email);
}

/**
 * 获取Action
 * 
 */
function get_action()
{
    $_CI = &get_instance();
    $action = $_CI->router->method;
    
    return $action;
}

//获取用户信息
function getuser_by_token($token) {
    
    if(!check_token($token))
    {
        $result["state_code"] = -1;
        $result["state_desc"] = "token令牌不合法";
        output($result);
    }
    
    $_CI = &get_instance();
    $_CI->load->model('liaoqiu_member_model');
    $return_lq_member = $_CI->liaoqiu_member_model->getMemberByToken($token);
    $result = array();
    if(count($return_lq_member)==0) {
        $result["state_code"] = -1;
        $result["state_desc"] = "token令牌不存在";
        output($result);
    }
    if($return_lq_member["token_status"]!="1" || $return_lq_member["status"]!="1") {
        if($return_lq_member["token_status"]!="1") {
            $result["state_code"] = -1;
            $result["state_desc"] = "token令牌有误，请重新登录";
        } else {
            $result["state_code"] = -2;
            $result["state_desc"] = "账号已被冻结，联系客服处理";
        }
        //output($result);
    }

    $now = time();
    $days = 2592000;//30天
    if($now - $return_lq_member['token_time'] > $days)
    {
        $result['state_code'] = -3;
        $result['state_desc'] = "token失效，请重新登录";
        //output($result);
    }
    return $return_lq_member;
    }
/**
 * 判断是否有中文字符串
 * 
 * @param string $content
 */    
function check_chinese($content)
{
    if(empty($content))
    {
        return true;
    }
    
    $pattern = "/[\x80-\xff]/";
    return preg_match($pattern, $content) ? true : false;
}

/**
 * 获取CI的配置值
 * 
 * @param string $field
 */
function get_config_field($field)
{
    $_CI = &get_instance();
    $return = $_CI->config->item($field);
    return $return;
}

/**
 * 创建上传目录
 * 
 * @param int $member_id
 */
function create_catalog($member_id)
{
    if(empty($member_id))
    {
        return '';
    }
    $upload_path = "./".  get_config_field('upload')."/member_image/";
    if(!is_dir($upload_path)) mkdir($upload_path,0777);
    
    $path = md5($member_id);
    $dir_arr = array(substr($path, 0, 2), substr($path, 2, 2), $member_id);
    foreach ($dir_arr as $k=>$v) 
        {
            $upload_path .= $v . "/";
            if(!is_dir($upload_path))
            {
                mkdir($upload_path, 0777);
            }
	}
    
        return $upload_path;
}

/**
 * 获取上传目录
 * 
 * @param int $member_id
 */
function get_catalog($member_id)
{
    if(empty($member_id))
    {
        return '';
    }
    $_CI = &get_instance();
    $upload_path = "./".  get_config_field('upload')."/member_image/";
    
    $path = md5($member_id);
    $upload_path .= substr($path, 0, 2) . '/' . substr($path, 2, 2) . '/';
    return $upload_path;
}


/**
 * 生成随机字符串
 * 
 * @param int $length
 */
function random_str($length = 5)
{
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_ ';
    $str = str_shuffle(str_shuffle($str));
    
    $limit = strlen($str);
    $start = $limit - $length;
    $start = rand(0, $start);
    return substr($str, $start, $length);
}

/**
 * 获取文件名称
 * 
 * @param string $tmp_name 文件名称
 * @param  string $upload_path 上传目录
 */
function get_filename($filename, $upload_path)
{
    if(empty($filename))
    {
        return '';
    }
    
    $type =  strtolower(strstr($filename, '.'));
    //文件名包含中文字符串处理
    if(check_chinese($filename))
    {
        $filename = 'IMG_' . date('Ymd'). substr(md5($filename), 10, 6) . $type;
    }
    
    
    $pic_path = $upload_path . $filename;
    //文件名已经存在
    if(is_file($pic_path))
    {
        $rnd_name = random_str(6);
        $filename = $rnd_name . $filename;
        $pic_path = $upload_path . $filename;
    }
    return $pic_path;
}

/**
 * 支持的图片类型
 * 
 * @return string
 */
function get_img_types()
{
    $file_type_arr = array(".gif",".jpg",".png",".jpeg");
    return $file_type_arr;
}

/**
 * 返回图片访问路径
 * 
 * @param string $path
 * @return string
 */
function get_access_path($path)
{
    $http = 'http://'.$_SERVER["HTTP_HOST"];
    return $http . $path;
}

/**
 * 加载模型
 * 
 * @param type $model
 */
function load_model($model, $prefix = 'liaoqiu_', $suffix = '_model')
{
    $_CI = &get_instance();
    $_CI->load->model($prefix . $model . $suffix);
    return $_CI;
}

/**
 * 自动生成昵称
 * 
 * @param string $nickname 邮箱或手机号
 * @param int $type 1:邮箱 2：手机号
 */
function auto_create_nickname($nickname, $type=1)
{
    if($type == 1)
    {
        $nickname = strtr($nickname, array('.' => '', '@' => '', 'com' => ''));   
    }
    
    $nickname = '球迷_' . $nickname;
    $nickname = check_account_exists($nickname);
    return $nickname;
}
/**
 * 检查用户名是否存在
 * 
 * @param string $nickname
 */
function check_account_exists($nickname)
{
    $_CI = load_model('member');
    $is_exists = $_CI->liaoqiu_member_model->getMemberBy5Uaccount($nickname);
    $length = 6;
    while (!empty($is_exists))
    {
        $nickname = $nickname . random_str($length ++);
        $is_exists = $_CI->liaoqiu_member_model->getMemberBy5Uaccount($nickname);
    }
    
    return $nickname;
}

/**
 * 检查是否是手机号码
 * 
 * @param string $mobile
 */
function check_mobile($mobile)
{
    $pattern = "/1[3458]{1}\d{9}$/";
    
    return preg_match($pattern, $mobile) ? true : false;
}


//上传文件，返回文件路径和参数
function upload_file($dir = 'other',$water=false)
	{
		    $upload_path = "./upload/".$dir."/";
		    $picname = @$_FILES['file']['name'];
            $picsize = @$_FILES['file']['size'];
            if ($picname != "") {
                if ($picsize > 1024000*8) {
					$data = array('file_url'=>'','desc'=>'大小不能超过8M');
                    output($data);
                }
			
                $type =  strtolower(strstr($picname, '.'));
                $file_type = file_type($type);
                $dir_arr = array('Y','m','多余');
			   foreach ($dir_arr as $k=>$v) {
				   	if(!is_dir($upload_path)) mkdir($upload_path,0777);
					if($k!=count($dir_arr)-1) $upload_path = $upload_path.date($v)."/";
			   }
                $rand = rand(100, 999);
                $pics = date("YmdHis") . $rand . $type;
                //上传路径
                $pic_path = $upload_path.$pics;
                if(move_uploaded_file(@$_FILES['file']['tmp_name'], $pic_path)) {
                $path = strstr($pic_path,'/');
	                if($water==true&&$file_type==0){
	                  //添加水印
						$_CI = &get_instance();
						$_CI->load->library('image_lib');
						$config['source_image'] = $pic_path;
						$config['wm_opacity'] = '50';
						$config['wm_type'] = 'overlay';
						$config['wm_overlay_path'] = './static/img/success.png';
						$config['wm_vrt_alignment'] = 'center';
						$config['wm_hor_alignment'] = 'center';
						$_CI->image_lib->initialize($config); 
					    $_CI->image_lib->watermark();              	
	                }
                    $file_url = 'http://'.$_SERVER["HTTP_HOST"].$path;
                    $data = array('file_url'=>$file_url,'file_type'=>$file_type,'desc'=>'上传成功' ,'file_path' => $pic_path, 'real_type' => $type);
					return $data;
                }else{
					$data = array('file_url'=>'','desc'=>'上传失败');
                	return $data;
                }
            }else{
				$data = array('file_url'=>'','desc'=>'没有文件');
            	return $data;
            }
	}

//返回文件类型
function file_type($type){
				$file_type_arr = array(
								'img'=>array(".gif",".jpg",".png",".jpeg"),
								'video'=>array(".avi",".mp4",".wmv",".3gp",".rmvb",".mpg"),
								);
               if (in_array($type,$file_type_arr['img'])) {
					return 0;  //图片
                }else if(in_array($type,$file_type_arr['video'])){
                	return  1;  //视频
                }else{
                	$data = array('file_url'=>'','desc'=>'暂时不支持该文件的格式！','type'=>$type);
                    output($data);
                }
}

//签名验证函数
function verify_sign()
{
    //echo "<pre>"; print_r($_SERVER);echo "</pre>";
    //测试key: 33#$59*8<@
    //正式key: aD5&cb>f4d
    //include("api_config.php");
    
    $_CI = get_instance();
	//设置各个参数的默认值
	$key = $_CI->config->item('api_key');
    $host = 'http://' . $_SERVER["HTTP_HOST"];
    $uri = $_SERVER["REQUEST_URI"] ;
    $uri = substr($uri, 0, strpos($uri, '?') + 1);
    $sign = @$_REQUEST["sign"];
    $params = $_REQUEST;
    if($sign==""){
        $ret = array(
            "state_code" => "-98",
            "state_desc" => "缺少签名参数sign！",
        );
        output($ret);
    } else {
        //$arr_request = explode("sign=", $param);// Divide uma string em strings
        //$param = $arr_request[0];
    }
	
    unset($params['sign'], $params['app_type'], $params['version']);
   
    $str_for_md5 = implode_array($params); 
    $str_md5 = md5(urlencode($host . $uri . $str_for_md5) . $key);
    //exit;
    if($sign != $str_md5){
        $ret = array(
            "state_code" => "-98",
            "state_desc" => "签名不正确, 我的签名明文是：".$str_for_md5." 签名后的md5值是：".$str_md5,
        );
        
        if(get_config_field('5u_environment')  == 'development')
        {
            $ret = array(
                "state_code" => "-98",
                "state_desc" => "签名不正确, 我的签名明文是：".$str_for_md5." 签名后的md5值是：".$str_md5 . ";正确的签名URL方式：" . $host . $uri . $str_for_md5 . ";参与MD5的值：urlencode(" .  $host . $uri . $str_for_md5  . ")" . $key,
            );
        }
       output($ret);
    }
}

function implode_array($params = array(), $field='')
{
    $return = '';
    ksort($params);
    if(is_array($params))
    {
        foreach ($params as $k => $v)
        {
            if(is_array($v))
            {
                $return .= implode_array($v, $k);
            }
            else 
            {
                if(!empty($field))
                {
                    $return .= $field . '[' . $k . ']=' . $v;
                }
                else{
                
                    $return .= $k . '=' . $v . '&';
                }
            }
        }
        $return = substr($return, 0, -1);
    }
    else 
    {
        $return = $params;
    }
    
    return $return;
}

//获取字符串的长度
function countstring($str) {
    $e = mb_detect_encoding($str, array('UTF-8', 'GBK'));
    if($e=="UTF-8"){
        $str = iconv("UTF-8", "GBK", $str);
    }
    return strlen($str);
}	

function get_upload_path($path , $type)
{
    $upload_path = $path . get_config_field('upload');
    $type_arr = array('show', 'head', 'member');
    
    if(!in_array($type, $type_arr))
    {
        return FALSE;
    }
    
    switch ($type)
    {
        case 'show':
            $upload_path .= '/show/';
            
            break;
        
        case 'head':
            
            break;
        
        case 'member':
            
            break;
    }
    
    return $upload_path;
}

/**
         * 获取粉丝数
         * 
         * @param int $member_id
         * @return int
         */
function get_fans_count($member_id)
{
    //粉丝数：
    load_model('friendship');
    $params = array(
        'b_id' => $member_id,
        'status' => Liaoqiu_friendship_model::STATUS_NORMAL,
    );
    $_CI = &get_instance();
    $fans_count = $_CI->liaoqiu_friendship_model->get_count($params);
    
    return $fans_count;
}

/**
 * 获取关注数
 * 
 * @param int $member_id
 * @return int
 */
function get_follows_count($member_id)
{
    load_model('friendship');
    //关注数:
            $params = array(
                'a_id' => $member_id,
                'status' => Liaoqiu_friendship_model::STATUS_NORMAL,
            );
            $_CI = &get_instance();
            
            $follows_count = $_CI->liaoqiu_friendship_model->get_count($params);
            
            return $follows_count;
}

/**
 * 获取爱队数据
 * 
 * @param int $member_id
 */
function get_focusteam($member_id)
{
    $focusteam = '尚未设置关注球队';
    
            load_model('focusteam');
            $_CI = &get_instance();
            $focus_team_arr = $_CI->liaoqiu_focusteam_model->getByMemberID($member_id);
            if(!empty($focus_team_arr) && !empty($focus_team_arr['focusteam']))
            {
                $params = array("team_id"=>$focus_team_arr['focusteam']);
                $result_5u_api = json_decode(usport_api("get_team_detail", $params), true);
                if(empty($result_5u_api['state_code']) && !empty($result_5u_api['team_detail']))
                {
                    $focusteam = '';
                    $i  = 0;
                    foreach ($result_5u_api['team_detail'] as $team)
                    {
                        $focusteam .= $team['team_name'] . ' ';
                        if(++ $i  > 7)
                        {
                            break; 
                        }
                    }
                    $focusteam = trim($focusteam);
                }
            }
    return $focusteam;
}

/**
 * 获取会员相册
 * 
 * @param int $member_id
 * @return array 相册URl
 */
function get_album($member_id)
{
    load_model('member_photo');
    $_CI = &get_instance();
            $list = $_CI->liaoqiu_member_photo_model->getRecordsByMemberID($member_id);
            $album = array();    
            if(!empty($list))
            {
                $i = 0;
                foreach ($list as $row)
                {
                    $album[] = array('img_url' => get_access_path($row['file_path']));
                    if(++ $i > 5)
                    {
                        break; 
                    }
                }
            }
            
            return $album;
}

/*
 * 通过路径获取图片
 */
function getImg($url,$dir='other') {
	if($url==""):return false;endif;
	$filename='./upload/'.$dir.strrchr($url,"/");
	ob_start();
	readfile($url);
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);
	if($size > 0){
		$fp2=@fopen($filename, "a");
		fwrite($fp2,$img);
		fclose($fp2);
	}else{
		$filename = 0;
	}
	return $filename;
}


/**
 * 生成群聊头像
 * 先将图片按比例生成 40 * 40的图片
 * 再合成在一个100 * 100的图片上面去。
 * 
 * @param array $src_image 需要合成群聊头像的图片地址
 */
function create_group_face($src_images, $group_id)
{
    if(empty($src_images) || !is_array($src_images))
    {
        return FALSE;
    }
    
    $server_url = 'http://'  . $_SERVER["HTTP_HOST"];
    $thumbs = $rounds = array();
    foreach ($src_images as $image)
    {
        $image_filepath = str_replace($server_url, '', $image);
        
        if(($pos = strpos($image_filepath, '?')) !== FALSE)
        {
            $image_filepath = substr($image_filepath, 0,  $pos);
        }
        
        $image_filepath = '.' . $image_filepath;
        if(!file_exists($image_filepath))
        {
            return FALSE;
        }
        
        $im = get_im_handler($image_filepath);
        
        if(!$im)
        {
            return FALSE;
        }
        
        //图片存在
        //则生成一张 40 * 40 的缩略图
        //缩略图生成失败
        if(!get_group_face_thumb_config($image_filepath))
        {
            return  FALSE;
        }
        
        $thumb_path  = str_replace('.jpg', '_thumb.jpg', $image_filepath) ;
        $round_save_path = create_round_face($thumb_path);
        $thumbs[] = $thumb_path; 
        $rounds[] = $round_save_path;
    }
    
    if(empty($rounds))
    {
        return false;
    }
    $group_config = get_group_config();
    $dst_image = @imagecreatetruecolor($group_config['width'], $group_config['height']) or die("Cannot Initialize new GD image stream");
    $backgroud_color = imagecolorallocate($dst_image, $group_config['default_red'], $group_config['default_green'], $group_config['default_blue']);
    imagefill($dst_image, 0, 0,$backgroud_color);
    foreach ($rounds as $i => $round)
    {
        $im = get_im_handler($round);
        
        if(!$im)
        {
            return FALSE;
        }
        
        $image = getimagesize($round);
        $src_w = $image[0];
        $src_h = $image[1];       
        
        imagecopyresampled($dst_image, $im, $group_config[$i]['x'], $group_config[$i]['y'], 0, 0, $group_config[$i]['width'], $group_config[$i]['height'], $src_w, $src_h);
    }
    
    $group_id_md5 = md5($group_id);
    $round_face_path = $group_config['save_path'] . DS . substr($group_id_md5, 0, 3) ;
    $success = true;
    if(!is_dir($round_face_path))
    {
        $success = mkdir($round_face_path, 0777, true);
        
    }
    
    if(!$success)
    {
        return FALSE;
    }
    
    $round_face_path .= DS . $group_id . '.jpg';
    imagejpeg($dst_image, $round_face_path);
    imagedestroy($dst_image);
    
    $round_face_path = ltrim(str_replace(APPPATH, '', $round_face_path), '.');
    
    return $round_face_path;
}

/**
 * 根据图片生成圆形
 * 
 * @param string $image_path
 */
function create_round_face($image_path)
{
    $original_image = get_im_handler( $image_path );

    /* get image size ... */
    $x = imagesx( $original_image );
    $y = imagesy( $original_image );
    $diameter = 40; 
    $radius = $diameter / 2;
    $cutted_image = imagecreatetruecolor( $diameter,$diameter );
    imagesavealpha($cutted_image, true);
    $group_config = get_group_config();
    $color = imagecolorallocatealpha($cutted_image, $group_config['default_red'], $group_config['default_green'], $group_config['default_blue'], $group_config['default_alpha']);
    imagefill($cutted_image, 0, 0, $color);
    
    for ( $x = 0; $x <= $radius; $x += 0.01 ) {

        /* standard form for the equation of a circle ... don't tell me you never knew that ... */
        $y = sqrt( $diameter * $x - pow( $x , 2 ) ) + $radius;
    
        /* i think i should call this successive scans ... */
        for ( $i = $x; $i < $diameter - $x; $i++ ) {

            /* half of the circle ... */
            imagesetpixel (
                    $cutted_image , $i, $y, 
                    imagecolorat( $original_image, $i, $y )
                                    );

            /* the other half of course ... */
            imagesetpixel ( 
                    $cutted_image , $i, $diameter - $y, 
                    imagecolorat( $original_image, $i, $diameter - $y ) 
                                    );

         }
            
    }
    
    /* avoid the white line when the diameter is an even number ... */
    if ( ! is_float( $radius ) )
            for ( $i = 0; $i < $diameter; $i++ )
                imagesetpixel ( 
                        $cutted_image , $i, $radius - 1,
                        imagecolorat( $original_image, $i, $radius - 1 )
                                        );
        
    $save_path = str_replace('_thumb', '_thumb_round', $image_path);
    $trans   = array(
      '.png' => '.jpg',
      '.gif' => '.jpg'
    );
    
    $save_path = strtr($save_path, $trans);
    /* show our work ... */
    imagejpeg( $cutted_image, $save_path);

    /* we have to cleaned up the mass before left ... */
    imagedestroy($original_image );
    imagedestroy($cutted_image );
    
    return $save_path;    
}
function get_im_handler($src_image)
{
    $im = false;
    $image = getimagesize($src_image);
        switch ($image[2])
        {
            case 1:
                $im = imagecreatefromgif($src_image);
                break;
            
            case 2 :
                $im = imagecreatefromjpeg($src_image);
                break;
            
            case 3 :
                $im = imagecreatefrompng($src_image);
                break;
        }
        
        return $im;
}

function get_group_config()
{
    $_CI = &get_instance();
    $_CI->load->config('group_face_config');
    
    $group_config = $_CI->config->item('group_face');
    
    return $group_config;
}

/**
 * 获取群头像缩略图的配置
 * 
 * @param string $src_image
 */
function get_group_face_thumb_config($src_image)
{

    if(empty($src_image))
    {
        return FALSE;
    }
    
    $size = getimagesize($src_image);
    
    if(!$size)
    {
        return FALSE;
    }
    
    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    $img_type = '';
    switch ($src_type)
    {
        case 1:
            $img_type = 'gif';
            break;
        case 2:
            $img_type = 'jpeg';
            break;
        case 3:
            $img_type = 'png';
            break;
        case 15:
            $img_type = 'wbmp';
            break;
        default :
            return FALSE;
    }
    
    $imagecreatefunc = 'imagecreatefrom' . $img_type;
    $src_img = $imagecreatefunc($src_image); //APPPATH . '../' . $src_image;
    
    $group_config = get_group_config();
    $dst_w = $group_config['thumb']['width'];
    $dst_h = $group_config['thumb']['height'];
    $dest_img = imagecreatetruecolor($dst_w, $dst_h);
    
    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    
    $imagefunc = 'image' . $img_type;
    $filename = str_replace('.jpg', '_thumb.jpg', $src_image);
    $imagefunc($dest_img, $filename);
    imagedestroy($src_img);
    imagedestroy($dest_img);

    return true;
}


//拼接分页条件
function pages($page_id,$page_type,$field)
{
	$where = '';
	if(is_numeric($page_id)&&is_numeric($page_type))
		$where .= $page_type==1 ? " and $field>$page_id":" and $field<$page_id";
	return $where;
}

/**
 * 腾讯云音频签名
 * 
 * @param type $member_id
 * @return type
 */
function qcloud_sig($member_id){
    
    $qcloud_sig_dir = get_config_field('qcloud_sig_dir');
    if(!file_exists($qcloud_sig_dir  . "/user_sig/"))
    {
        mkdir($qcloud_sig_dir . "/user_sig/", 0777, true);   
    }
    
    $qcloud_sig_dir = realpath($qcloud_sig_dir);
    $qcloud_url = $qcloud_sig_dir . "/";
    $sig_file_url = $qcloud_url."user_sig/".$member_id."_sig";
        /*
        $json_file_url = $qcloud_url."user_sig/".$member_id."_json";
        //$fp = file_get_contents($json_file_url);
		if(file_exists($json_file_url)) {
			$json = '{
                "TLS.account_type": "169",
                "TLS.identifier": "'.$member_id.'",
                "TLS.appid_at_3rd": "1400000352",
                "TLS.sdk_appid": "1400000352",
                "TLS.expire_after": "86400"
            }';
			file_put_contents($json_file_url, $json);
		}
        //获取签名：[.//data/www/tls_sig_api/lib/tls_licence_tools 1 私钥文件 strAppid3rd,dwSdkAppid,strIdentifier,dwAccountType,dwExpire,签名文件]
        //验证签名：[.//data/www/tls_sig_api/lib/tls_licence_tools 2 签名文件 公钥文件 strAccountType,strAppid3Rd,strAppid,strIdentify
        */

    $array_qcloud = array(
        "account_type" => get_config_field('qcloud_account_type'),
        "identifier" => $member_id,
        "appid_at_3rd" => get_config_field('qcloud_appid_at_3rd'),
        "sdk_appid" => get_config_field('qcloud_sdk_appid'),
        "expire_after" => get_config_field('qcloud_expire_after'),    //一个月
        'sig_file' => get_config_field('qcloud_sig_file'),
    );
    $install_path = get_config_field('qcloud_install_path');
    
    $str_command = $install_path . "/tls_licence_tools 1 " . $array_qcloud['sig_file'] ." " .$sig_file_url ." ".$array_qcloud["expire_after"] ." ".$array_qcloud["sdk_appid"]." ".$array_qcloud["account_type"]. " ".$array_qcloud["appid_at_3rd"]." ".$array_qcloud["identifier"];
    
    $sig_file = exec($str_command, $output, $returnvar);
    $sig = "";
    $sig = @file_get_contents($sig_file_url);
    
    return  $sig;
        
    }
