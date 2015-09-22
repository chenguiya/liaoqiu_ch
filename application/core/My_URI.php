<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_URI extends CI_URI {
    // 记录被调用过的最大 segment 序号，默认=2 (1=Controller, 2=action) 加八个参数
    var $_goodseg = 10;

    // HOOK: 记录曾经使用过的 segment 的最大序号，用来过滤垃圾 seg
    function segment($n, $no_result = FALSE)
    {
      if ($n > $this->_goodseg)
      {
       $this->_goodseg = $n;
      }
      return parent::segment($n, $no_result);
    }
     
    // EXTEND: 只获取曾经被 ::segment() 方法调用过的最大序号之内的 segment 数组集合
    function good_rsegment_array() 
    {
       return array_slice($this->rsegments, 0, $this->_goodseg);
    }
    
    //URI过滤 允许中文
    function _filter_uri($str)
    {
        
        if ($str != '' AND $this->config->item('permitted_uri_chars') != '')
        {
            $str = urlencode($str);
            // if ( ! preg_match("|^[".preg_quote($this->config->item('permitted_uri_chars'))."]+$|i", $str))   
            if ( ! preg_match("|^[".($this->config->item('permitted_uri_chars'))."]+$|i", rawurlencode($str))) 
            {
                exit('The URI you submitted has disallowed characters.');
            }
            $str = urldecode($str);
        }
        return $str;
    }
} 
?>