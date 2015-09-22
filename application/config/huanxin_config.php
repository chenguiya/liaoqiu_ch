<?php
/* *************************************************************************************
 * 环信聊天SDK配置文件
 * 版本：2.1.3
 * 日期：2015-8-18
 * 创建：liu
 * 说明：
 * 提示：如何获取配置文件的相当参数值
 * 1.登录环信官方网站后台(www.easemob.com), 登录账号:5usport, 邮箱: libo@5usport.com；a123456
 * 2.点击“我的应用”(https://console.easemob.com/app_list.html)；
 * 3.点击“liaogeqiu”查看应用详情，点击“应用概况”标签；
 ***************************************************************************************/
$config = array();

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
$host_arr = array("www.5ulq.com","www.5usport.com","lq.5usport.com","avatar.5usport.com","uadmin.usportnews.com");
if(in_array($_SERVER["HTTP_HOST"],$host_arr)){
    //合作企业账号，就是注册时填写的企业ID
    $config['org_name']      	= '5usport';
    //应用ID，（聊个球产品的应用ID为：liaogeqiu）
    $config['app_name']      	= '5usportim';
    //应用标识(key)，格式为:{企业ID}#{AppID}
    $config['appkey']       	= '5usport#5usportim';
    //client_id: 环信需要提供的，不知道干嘛用的
    $config['client_id']			= 'YXA60QffwM1BEeSxyCWl-5qpjQ';
    //client_secret: 环信需要提供的，不知道干嘛用的
    $config['client_secret']	= 'YXA6heCKMbgd5oW8f8XxqmlkoVy-nc0';
} else {
    //合作企业账号，就是注册时填写的企业ID
    $config['org_name']      	= '5usport';
    //应用ID，（聊个球产品的应用ID为：liaogeqiu）
    $config['app_name']      	= 'liaogeqiu';
    //应用标识(key)，格式为:{企业ID}#{AppID}
    $config['appkey']       	= '5usport#liaogeqiu';
    //client_id: 环信需要提供的，不知道干嘛用的
    $config['client_id']			= 'YXA6I-ds8IwCEeSCwWMx5dVyZg';
    //client_secret: 环信需要提供的，不知道干嘛用的
    $config['client_secret']	= 'YXA6MWOxqDpF9qN0qB-g3vWytiEmxAc';

}
    //token.txt的服务器存放目录: token，7天获取一次，保存服务器本地
    $config['token_dir']	= 'upload/token/';
    //用户上传的图片需要上传到聊球服务器，保存服务器本地
    $config['uploads_images_dir']	= 'upload/huanxin/images/';
    //request_url: 请求环信服务器地址URL。 此版本不需要配置此参数，可为空
    //$config['url']		= 'https://a1.easemob.com';


//签名方式 不需修改
$config['sign_type']    = 'MD5';

//字符编码格式 目前支持 gbk 或 utf-8
$config['input_charset']= 'utf-8';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$config['transport']    = 'http';
?>