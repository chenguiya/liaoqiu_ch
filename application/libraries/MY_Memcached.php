<?php
/*
 * Memcached缓存
 */
class MY_Memcached {
    private $_CI;
    private $prefix = "lq_";
    public function __construct()
    {
        $this->_CI = & get_instance();
        if( ! $this->_CI->load->is_loaded('cache'))
        {
            $this->_CI->load->driver(
                'cache',
                array('adapter' => 'memcached', 'backup' => 'file'
            ));
        }
    }
    // expire 失效时间为秒
    public function set($key, $var, $expire)
    {
    	$key = $this->prefix.$key;
        return $this->_CI->cache->save($key, $var, $expire);
    }
    
    // 取值
    public function get($key)
    {
    	$key = $this->prefix.$key;
        return $this->_CI->cache->get($key);
    }
    
    // 删除
    public function delete($key)
    {
    	$key = $this->prefix.$key;
        return $this->_CI->cache->delete($key);
    }	
}