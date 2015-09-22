<?php
// 输出HTML
require_once('configs.global.php');
require_once('avatar.php');

$objAvatar = new Avatar();
$code_data = trim($_GET['code_data']);
$code_data = str_replace('____', '%2', $code_data); // ____变成%2
$json_data = $objAvatar->uc_authcode(urldecode($code_data), 'DECODE', UC_KEY);
$arr_ci_info = json_decode($json_data, TRUE);

$my_sign = md5(md5('avurl'.$arr_ci_info['time'].$arr_ci_info['userid'].UC_KEY));
$time = time();

if(!is_array($arr_ci_info))
{
    echo "数据传送错误，请重试";
}
elseif($time - (trim($_GET['time']) + 0) > 600) // 密文有效期，单位 秒
{
    echo "操作超时，请新刷新页面";
}
elseif($arr_ci_info['time'] != trim($_GET['time']) + 0)
{
    echo "参数不正确，请重试";
}
elseif($my_sign != trim($_GET['sign']))
{
    echo "签名不正确，请重试";
}
else
{
    $uid = $arr_ci_info['userid'] + 0;
    
    echo $objAvatar->avatar_show($uid, 'middle');
    echo "&nbsp;&nbsp;";
    echo $objAvatar->uc_avatar($uid);
}
