<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Output extends CI_Output
{
    function get_cache_URI($set_uri = NULL)
    {
        $CFG =& load_class('Config');
        $URI =& load_class('URI');
        
        $set_uri = (isset($set_uri)) ? $set_uri : $URI->uri_string;
        
        $cache_path = ($CFG->item('cache_path') == '') ? APPPATH.'../cache/' : $CFG->item('cache_path');    // 平级
        //$cache_path = ($CFG->item('cache_path') == '') ? BASEPATH.'cache/' : $CFG->item('cache_path');    // 不使用BASE
        
        
        //echo $cache_path;
        if(!is_dir($cache_path) OR !is_writable($cache_path))
        {
            return FALSE;
        }
        
        $uri = $CFG->item('base_url').
               $CFG->item('index_page').
               $set_uri;
        
        return array('path' => $cache_path, 'uri' => $uri);
    }
    
    // 生成目录
    function mkdirs($dir, $mode = 0777, $recursive = TRUE)
    {
        if(is_null($dir) || $dir === "")
        {
            return FALSE;
        }
        if(is_dir($dir) || $dir === "/")
        {
            return TRUE;
        }
        if($this->mkdirs(dirname($dir), $mode, $recursive))
        {
            return mkdir($dir, $mode);
        }
        return FALSE;
    }
    
    // 清页面缓存 
    function clear_page_cache($set_uri = NULL, $filepath = NULL)
    {
        $set_uri = str_replace("/", "+", $set_uri);
        $filepath = str_replace("/", "+", $filepath);
        
        $cacheuri = $this->get_cache_URI($set_uri);
        
        if(isset($filepath))
        {
            $filepath = $cacheuri['path'].$set_uri.'/'.$filepath;
        }
        else
        {
            $filepath = $cacheuri['path'].$set_uri;
        }
        
        $this->removeDir($filepath);
    }
    
    // 删除目录或文件
    function removeDir($dirName)
    {
        //echo $dirName;
        $result = false;

        if(!is_dir($dirName))
        {
            if(is_file($dirName))
            {
                
                return unlink($dirName);    // 删除一个文件
            }
            else
            {
                return NULL;
            }
        }
        
        $handle = opendir($dirName);
        while(($file = readdir($handle)) !== false)
        {
            if($file != '.' && $file != '..')
            {
                $dir = $dirName . DIRECTORY_SEPARATOR . $file;
                is_dir($dir) ? removeDir($dir) : unlink($dir);
            }
        }
        closedir($handle);
        $result = rmdir($dirName) ? true : false;
        return $result;
    }
    
    // $param=1 写入文件缓存 $param=0 读取文件缓存 
    // m1 zhangjh 2014-10-28 多传递 uri
    // m2 zhangjh 2014-11-21 支持多域名修改
    function breakup_cachefiles($cache_path, $param = 1, $uri, $arr_uri = array())
    {
        $ret = '';
        
        //echo "<pre>";
        //print_r($arr_uri);
        
        /*1、取得md5码的后2个字母，目的是分散缓存到不同的文件夹，使磁盘能够更快索引
             substr($cache_path, -3) C(36,3) = 7140（排列组合数），磁盘可以承受再大就不行了
        */
        
        //echo $cache_path."\n";
        
        $md = $arr_uri["0"]."+".$arr_uri["1"]; // (0=Controller, 1=action)
        $md = str_replace("/", "", $md);
        $cache_path .= $md;
        
        if($param == 1)
        {
            $this->mkdirs($cache_path);
        }
        
        // $cache_path .= "/".md5($uri); // 用其他参数为文件名
        $file_name = '';
        for($i = 2; $i < count($arr_uri); $i++)
        {
           $file_name .= $arr_uri[$i].'+';
        }
        
        // m2
        if($file_name == '') // 取不到arr_uri时,用index为名字
            $file_name = 'index+';
            
        $file_name = substr($file_name, 0, strlen($file_name) - 1);
        $cache_path .= "/".$file_name;
        
        
        
        //echo $cache_path."|";
        //
        //echo $param."|";

        // $md5_2 = substr($cache_path, -2);
        //echo '<font color=blue>'.$md5_2.'<font>';
        //2、建立目录名=$md5_2的目录
        $dir = dirname($cache_path);    //获取当前目录名
        $file = basename($cache_path);  //获取当前文件名
        //$newdir = $dir.'/'.$md5_2;      //新的目录名
        /* if($param == 1)
        {
            if (!file_exists($newdir))//目录不存在则创建
                mkdir($newdir,0777);
        }
        */
        //3、将    $cache_path 定位到新的文件夹
        $ret = $dir.'/'.$file;
        //echo $ret;
        //if($param==1){
        //    echo $ret;}
        //else{    
        //    echo '<font color=red>'.$ret.'<font>';}
        
        return $ret;
    }
    
    // --------------------------------------------------------------------

    /**
     * Write a Cache File
     *
     * @access    public
     * @param     string
     * @return    void
     */
    function _write_cache($output)
    {
        $CI = &get_instance();
        $t = $CI->uri->uri_string;

        if ('/' == substr($t, 0, 1))
        {
            $CI->uri->uri_string = $CI->uri->uri_string;
        }
  
        $CI =& get_instance();
        $path = $CI->config->item('cache_path');

        $cache_path = ($path == '') ? APPPATH.'cache/' : $path;

        if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
        {
            log_message('error', "Unable to write cache file: ".$cache_path);
            return;
        }

        $uri =  $CI->config->item('base_url').
                $CI->config->item('index_page').
                $CI->uri->uri_string();
                
        $arr_uri = $CI->uri->good_rsegment_array(); // 在 MY_URI 定义这个函数
        //echo "<pre>";
        //print_r($arr);
                
                
        // die($CI->config->item('base_url')."|".$CI->config->item('index_page')."|".$CI->uri->uri_string());
        // $cache_path .= md5($uri);
        
        /*生成Md5缓存文件后处理*/
        //echo $cache_path;return;
        $cache_path = $this->breakup_cachefiles($cache_path, 1, $uri, $arr_uri);
        
        if ( ! $fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
        {
            log_message('error', "Unable to write cache file: ".$cache_path);
            return;
        }

        $expire = time() + ($this->cache_expiration * 60);

        if (flock($fp, LOCK_EX))
        {
            fwrite($fp, $expire.'TS--->'.$output);
            flock($fp, LOCK_UN);
        }
        else
        {
            log_message('error', "Unable to secure a file lock for file at: ".$cache_path);
            return;
        }
        fclose($fp);
        @chmod($cache_path, FILE_WRITE_MODE);

        log_message('debug', "Cache file written: ".$cache_path);
    }

    // --------------------------------------------------------------------

    /**
     * Update/serve a cached file
     *
     * @access    public
     * @param     object    config class
     * @param     object    uri class
     * @return    void
     */
    function _display_cache(&$CFG, &$URI)
    {
        $cache_path = ($CFG->item('cache_path') == '') ? APPPATH.'cache/' : $CFG->item('cache_path');

        // Build the file path.  The file name is an MD5 hash of the full URI
        $uri =  $CFG->item('base_url').
                $CFG->item('index_page').
                $URI->uri_string;
        $filepath = $cache_path.md5($uri);
        
        /*生成Md5缓存文件后处理*/
        //die($cache_path."|".$CFG->item('base_url')."|".$CFG->item('index_page')."|".$URI->uri_string());
        $arr_uri = $URI->good_rsegment_array();
        $filepath = $this->breakup_cachefiles($cache_path, 0, $uri, $arr_uri);
        
        if ( ! @file_exists($filepath))
        {
            return FALSE;
        }

        if ( ! $fp = @fopen($filepath, FOPEN_READ))
        {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';
        if (filesize($filepath) > 0)
        {
            $cache = fread($fp, filesize($filepath));
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        // Strip out the embedded timestamp
        if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
        {
            return FALSE;
        }

        // Has the file expired? If so we'll delete it.
        if (time() >= trim(str_replace('TS--->', '', $match['1'])))
        {
            if (is_really_writable($cache_path))
            {
                @unlink($filepath);
                log_message('debug', "Cache file has expired. File deleted");
                return FALSE;
            }
        }

        // Display the cache
        $this->_display(str_replace($match['0'], '', $cache));
        log_message('debug', "Cache file is current. Sending it to browser.");
        return TRUE;
    }
}
    
// END class MY_Output
?>