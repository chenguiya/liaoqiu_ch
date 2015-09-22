<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * 七牛图片地址转换
 * type = 1 转换纯 http://xxx.xxx/xx.jpg
 * type = 2 转换含 'url' => 'http://xxx.xxx/xx.jpg', 的数组形字符串
 */
function qiniu_change($content, $type = 1)
{
    $_CI =& get_instance();
    $_CI->config->load('common_config');

    $qiniu_use = $_CI->config->item('qiniu_use') + 0;
    $qiniu_url = trim($_CI->config->item('qiniu_url'));

    if($type == 1)
    {
        $content = preg_replace('/(.*?)\/uploadfile\/(.*?)/', $qiniu_url."/uploadfile/\\2", $content);
    }
    elseif($type == 2)
    {
        $content = preg_replace('/\'url\'\s=\>\s\'(.*?)\/uploadfile\/(.*?)\'\,/', "'url' => '".$qiniu_url."/uploadfile/\\2',", $content);
    }

    return $content;
}

// 检查七牛本地缩略图文件是否存在
function local_qiniu_exists($url_path)
{
    // $phpcms_path = '/data/www/usport/';  // usport 绝对路径
    // $ci_path = '/data/www/5usport/';     // 5usport绝对路径
    $upload_path = dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'usport'.DS.'uploadfile'.DS;    // phpcms的上传路径
    $filename = $upload_path.str_replace('/', DS, $url_path);
    if(@file_exists($filename))
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

// 写七牛本地缩略图
function local_qiniu_write($qiniu_thumb_url, $thumb_url_tail)
{
    $bln_return = FALSE;
    $upload_path = dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'usport'.DS.'uploadfile'.DS;    // phpcms的上传路径

    $input = curl_access($qiniu_thumb_url); // 缩略图内容
    $file = $upload_path.$thumb_url_tail;   // 本地缩略图全路径

    $handle = @fopen($file, "w");
    if(!@fwrite($handle, $input))
    {
        $bln_return = FALSE;
    }
    else
    {
        $bln_return = TRUE;
    }
    @fclose($handle);
    return $bln_return;
}

/**
 * 七牛图片缩略图保存到本地-适用于所有
 * @param $arr_pic 处理过的图片路径
 * @param $arr_data 图片参数
 */
function qiniu_thumb_store_general($arr_pic,$arr_data,$field='thumb'){
    //$arr_return = array();
    for($i = 0; $i<count($arr_pic); $i++)
    {
        if($arr_data['is_promote'] == 2)    // 2 表示第一次取外部图片链接生成缩略图，转到本地后供日后使用
        {
            //$arr_return[$i] = $arr_pic[$i];
            //$arr_pic[$i]->$field = qiniu_change($arr_pic[$i]->$field);

            // 查询原来是否有这个尺寸的图片
            $arr_url = explode('/uploadfile/', $arr_pic[$i]->$field);

            $url_head = $arr_url[0];    // 本地URL原图片路径头
            $url_tail = $arr_url[1];    // 本地URL原图片路径尾

            $arr_url_sub = explode('.', $arr_url[1]);

            $thumb_url_tail = $arr_url_sub[0].'_'.$arr_data['mode'].'_'.$arr_data['width'].'_'.$arr_data['height'].'.'.$arr_url_sub[1];
            $thumb_url = $arr_url[0].'/uploadfile/'.$thumb_url_tail;    // 要生成本地的缩略图URL

            $thumb_pic_exist = local_qiniu_exists($thumb_url_tail); // 对应的缩略图是否存在

            if(substr_count($_SERVER['HTTP_HOST'], '.5usport.com') > 0) // 是外网正式服务器
            {
                if(!$thumb_pic_exist) // 如果没有这个尺寸的图片,要生成本地的缩略图
                {
                    $_CI =& get_instance();
                    $_CI->config->load('common_config');

                    $qiniu_thumb_url_head = $_CI->config->item('qiniu_url');
                    $qiniu_thumb_url = $qiniu_thumb_url_head.'/uploadfile/'.$url_tail.'?'.'imageView/'.$arr_data['mode'].'/w/'. $arr_data['width'].'/h/'.$arr_data['height'];
                    $bln_write_success = local_qiniu_write($qiniu_thumb_url, $thumb_url_tail);

                    if($bln_write_success === TRUE) // 如果生成成功，则用本地缩略图的URL
                    {
                        $arr_pic[$i]->$field = $url_head.'/uploadfile/'.$thumb_url_tail;
                    }
                    else    // 生成失败，用七牛的缩略图URL
                    {
                        $arr_pic[$i]->$field = $qiniu_thumb_url;
                    }
                }
                else    // 如果有这个尺寸的图片，用本地缩略图的URL
                {
                    $arr_pic[$i]->$field = $url_head.'/uploadfile/'.$thumb_url_tail;
                }
            }
            else
            {
                $arr_pic[$i]->$field = $arr_pic[$i]->$field;
            }
        }
        elseif($arr_data['is_promote'] == 1)    // 1 表示使用外部图片链接，如七牛
        {
            $arr_pic[$i]->$field = qiniu_change($arr_pic[$i]->$field);
            if($arr_data['mode']+0 != 0 && $arr_data['width'] + 0 != 0 && $arr_data['height'] + 0 != 0) // 使用缩略图
            {
                $arr_pic[$i]->$field = $arr_pic[$i]->$field.'?'.'imageView/'.$arr_data['mode'].'/w/'. $arr_data['width'].'/h/'.$arr_data['height'];
            }
        }

    }

    return $arr_pic;

}

/**
 * 七牛图片缩略图保存到本地
 * @param $arr_pic 处理过的图片路径
 * @param $arr_data 图片参数
 */
function qiniu_thumb_store($arr_pic, $arr_data)
{
    $arr_return = array();

    for($i = 0; $i < count($arr_pic); $i++)
    {
        $arr_return[$i] = $arr_pic[$i];

        if($arr_data['is_promote'] == 0)        // 0 表示使用原始URL
        {}
        elseif($arr_data['is_promote'] == 1)    // 1 表示使用外部图片链接，如七牛
        {
            if($arr_data['mode']+0 != 0 && $arr_data['width'] + 0 != 0 && $arr_data['height'] + 0 != 0) // 使用缩略图
            {
                $arr_return[$i]['url'] = $arr_pic[$i]['url'].'?'.'imageView/'.$arr_data['mode'].'/w/'. $arr_data['width'].'/h/'.$arr_data['height'];
            }
        }
        elseif($arr_data['is_promote'] == 2)    // 2 表示第一次取外部图片链接生成缩略图，转到本地后供日后使用
        {
            // 查询原来是否有这个尺寸的图片
            $arr_url = explode('/uploadfile/', $arr_pic[$i]['url']);
            $url_head = $arr_url[0];    // 本地URL原图片路径头
            $url_tail = $arr_url[1];    // 本地URL原图片路径尾

            $arr_url_sub = explode('.', $arr_url[1]);
            $thumb_url_tail = $arr_url_sub[0].'_'.$arr_data['mode'].'_'.$arr_data['width'].'_'.$arr_data['height'].'.'.$arr_url_sub[1];
            $thumb_url = $arr_url[0].'/uploadfile/'.$thumb_url_tail;    // 要生成本地的缩略图URL

            $thumb_pic_exist = local_qiniu_exists($thumb_url_tail); // 对应的缩略图是否存在

            if(substr_count($_SERVER['HTTP_HOST'], '.5usport.com') > 0) // 是外网正式服务器
            {
                if(!$thumb_pic_exist) // 如果没有这个尺寸的图片,要生成本地的缩略图
                {
                    $_CI =& get_instance();
                    $_CI->config->load('common_config');

                    $qiniu_thumb_url_head = $_CI->config->item('qiniu_url');
                    $qiniu_thumb_url = $qiniu_thumb_url_head.'/uploadfile/'.$url_tail.'?'.'imageView/'.$arr_data['mode'].'/w/'. $arr_data['width'].'/h/'.$arr_data['height'];
                    $bln_write_success = local_qiniu_write($qiniu_thumb_url, $thumb_url_tail);

                    if($bln_write_success === TRUE) // 如果生成成功，则用本地缩略图的URL
                    {
                        $arr_return[$i]['url'] = $url_head.'/uploadfile/'.$thumb_url_tail;
                    }
                    else    // 生成失败，用七牛的缩略图URL
                    {
                        $arr_return[$i]['url'] = $qiniu_thumb_url;
                    }
                }
                else    // 如果有这个尺寸的图片，用本地缩略图的URL
                {
                    $arr_return[$i]['url'] = $url_head.'/uploadfile/'.$thumb_url_tail;
                }
            }
            else
            {
                $arr_return[$i]['url'] = $arr_pic[$i]['url'];
            }
        }
    }
    return $arr_return;
}
