<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//第三方登录(QQ)
$config['qq_appid']='100458914';
$config['qq_appkey']='4c2fa19669b474c27fcd2a4b3671013e';
$config['qq_callback']='http://www.5usport.com/member/qqlogin/';

//第三方登录（新浪）
$config['sina_akey']='4273364693';
$config['sina_skey']='e89f21c5001acf0348cd940c19e52ea6';

$config['qiniu_use'] = 1;   // 是否使用七牛总开关
$config['qiniu_url'] = 'http://7sbrzn.com1.z0.glb.clouddn.com';     // 我的账号 七牛域名
$config['qiniu_url'] = 'http://img-7n.5usport.com';                 // 公司账号 自定义域名

//会员头像上传路径
$config['avatar_api_dir'] = APPPATH . '../upload/head/';
$config['avatar_api_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/upload/head';


//$config['avatar_api_dir'] = "http://api.5usport.com/avatar";//liu+
//$config['avatar_api_url'] = "http://api.5usport.com/avatar";//liu+
//与聊球接口的数组
$config['api_url']      = '';                                       //聊球接口地址；默认为空
$config['api_key']      = '';                                       //聊球接口密钥；默认为空
$config['environment']  = '';                                       //聊球接口环境；默认为空
$host_arr = array("www.5ulq.com","www.5usport.com","lq.5usport.com","avatar.5usport.com","uadmin.usportnews.com");
if(in_array($_SERVER["HTTP_HOST"],$host_arr)){
	//外网
	$config['domain']      = 'http://lq.5usport.com';            		//域名
    $config['api_url']      = 'http://api.liaoqiu.5usport.com';          //聊球接口地址
    $config['api_key']      = 'aD5&cb>f4d';                         //聊球接口密钥
    $config['environment']  = 'production';                         //聊球接口环境
    $config['5u_api_url']      = 'http://api.liaoqiu.5usport.com';          //5U主站接口地址
    $config['5u_api_key']      = '3#u29As9Fj23';                    //5U主站接口密钥
    $config['5u_environment']  = 'production';                     //5U主站接口环境
} else {
	//内网
	$config['domain']      = 'http://liaoqiu.usport.cc';            //域名
    $config['api_url']      = 'http://www.usport.cc';               //聊球接口地址
    $config['api_key']      = '33#$59*8<@';                         //聊球接口密钥
    $config['environment']  = 'development';                        //聊球接口环境
    $config['5u_api_url']      = 'http://www.usport.cc';          	//聊球接口地址
    $config['5u_api_key']      = 'a@39e8a53Qs';                     //聊球接口密钥
    $config['5u_environment']  = 'development';            			//聊球接口环境
}

//腾讯云视频文件保存地址
$config['qcloud_sig_dir'] = APPPATH . '../upload/qcloud';
if($config['5u_environment'] == 'production')
{
    $config['qcloud_install_path'] = '/data/www/tls_sig_api-linux-64/tools';
    $config['qcloud_account_type'] = "262";
    $config['qcloud_appid_at_3rd'] = "1400000677";
    $config['qcloud_sdk_appid'] = "1400000677";
    $config['qcloud_expire_after'] = "7776000";    //一个月
    $config['qcloud_sig_file'] = '/data/www/liaoqiu/upload/certs/ec_key.pem';
}
 else {
    //测试
     $config['qcloud_install_path'] = '/data/www/tls_sig_api-linux-64/tools';
     $config['qcloud_account_type'] = "262";
     $config['qcloud_appid_at_3rd'] = "1400000677";
     $config['qcloud_sdk_appid'] = "1400000677";
     $config['qcloud_expire_after'] = "7776000";    //一个月
     $config['qcloud_sig_file'] = '/data/www/liaoqiu/upload/certs/ec_key.pem';
}