<?php
/**
 * Api类
 * @author zhangjh zhangjianhua@usportnews.com
 * @date 2014-11-04
 */

class Api_out
{
	const JSON = 'Json';
	const XML = 'Xml';
	const ARR = 'Array';
    protected $phpcms_url;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->config->load('common_config');
        $ps_api_url = $this->_CI->config->item('ps_api_url');

        $this->phpcms_sso = $ps_api_url.'/api.php?op=ci&action=';   // phpcms UC 接口
        $this->phpcms_key = 'cOEnrinvgZ0UpZZXxaWO';
    }

    /**
     * 360图文接口 by luojing
     */
    public function get_360tw()
    {
        $return = array();
        // return $return;

        $cache_name = "tw360";
        $_CI =& get_instance();
        $_CI->load->library('memcache');
        $cache = $_CI->memcache->get($cache_name);

        if(!empty($cache)) // 如缓存有数据
        {
            $arr_360tw = $cache;
        }
        else
        {
            $pararl = "http://sh.qihoo.com/api/out_rst_usportnews.json";
            $data = trim(curl_access($pararl));
            $pure_data = substr($data, 11, -2);
            $arr_360tw = json_decode($pure_data, true);
            $_CI->memcache->set($cache_name, $arr_360tw, 600);
        }

        for($i = 0; $i < count($arr_360tw); $i++)
        {
            $arr_360tw[$i]['short_title'] = str_intercept($arr_360tw[$i]['title'], 0, 11);
            $arr_360tw[$i]['url'] = urldecode($arr_360tw[$i]['url']);
        }
        $return['tw360'] = $arr_360tw;

        $hot_word_url = 'http://sh.qihoo.com/hot/text.html?site=usportnews.com&src=out_usportnews&data=sports&n=10&column=2';
        $hot_word_json = 'http://sh.qihoo.com/api/partner_hq.json?site=usportnews.com&src=out_usportnews&data=sports&n=10&column=2';

        $data = trim(curl_access($hot_word_json));
        $pure_data = substr($data, 11, -1);
        $arr_hot_word = json_decode($pure_data, true);

        $return['hot_word_url'] = $arr_hot_word;

        return $return;
    }

    /**
     * 与phpcms交互
     */
    public function phpcms($param)
    {
        $time = time();
        $action = $param['action'];
        $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));

        // 取UC配置
        if($action == 'get_config_uc')
        {
            $url = $this->phpcms_sso.$action.'&time='.$time.'&sign='.$sign;
            //echo $url;
        }

        // 同步登录
        if($action == 'set_synlogin_uc')
        {
            $param['time'] = $time;
            $json_data = json_encode($param);
            $code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
            $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
            $url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
            //echo $url;
        }

        // 同步注册
        if($action == 'set_synregister_uc')
        {
            $param['time'] = $time;
            $json_data = json_encode($param);
            $code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
            $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
            $url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
        }

        // 同步登出
        if($action == 'set_synlogout_uc')
        {
            $param['time'] = $time;
            $json_data = json_encode($param);
            $code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
            $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
            $url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
        }

        // 同步修改
        if($action == 'set_synupdate_uc')
        {
            $param['time'] = $time;
            $json_data = json_encode($param);
            $code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
            $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
            $url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
            //echo $url;
        }
        
        // 根据ID取账号
        if($action == 'get_username_uc')
        {
            $param['time'] = $time;
            $json_data = json_encode($param);
            $code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
            $sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
            $url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
        }
		
		// 根据账号取ID
		if($action == 'get_userid_uc')
		{
			$param['time'] = $time;
			$json_data = json_encode($param);
			$code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
			$sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
			$url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
		}
		
		// 根据Email取ID
		if($action == 'get_userid_uc_by_email')
		{
			$param['time'] = $time;
			$json_data = json_encode($param);
			$code_data = urlencode(decode($json_data, 'E', $this->phpcms_key));
			$sign = md5(md5('ci'.$action.$time.$this->phpcms_key));
			$url = $this->phpcms_sso.$action.'&code_data='.$code_data.'&time='.$time.'&sign='.$sign;
		}

        $return = curl_access($url);
        return json_decode($return, TRUE);
    }

    public function show($data, $format = self::JSON, $code, $message, $target)  {
    	$format = ucfirst($format);
    	$classname = strtolower($format).'builder';
    	$this->_CI->load->library('response/'.$format.'builder');
    	$this->_CI->$classname->response($data,$target,$code,$message);
    }
}
