<?php
// 参考 system\helpers\cookie_helper.php 里面的功能，形成一个类

class MY_Cookie {

    public function __construct() {}
    
    /**
     * Set cookie
     *
     * Accepts six parameter, or you can submit an associative
     * array in the first parameter containing all the values.
     *
     * @access  public
     * @param   mixed
     * @param   string  the value of the cookie
     * @param   string  the number of seconds until expiration
     * @param   string  the cookie domain.  Usually:  .yourdomain.com
     * @param   string  the cookie path
     * @param   string  the cookie prefix
     * @return  void
     */
    public function set($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE)
    {
        // Set the config file options
        $CI =& get_instance();
        
        if(is_array($name)) // 如果第一个参数是数组的话
        {
            foreach($name as $key => $val)
            {
                $CI->input->set_cookie($key, $val, $expire, $domain, $path, $prefix, $secure);
            }
        }
        else
        {
            $CI->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure);
        }
    }
    
    /**
     * Fetch an item from the COOKIE array
     *
     * @access  public
     * @param   string
     * @param   bool
     * @return  mixed
     */
    function get($index = '', $xss_clean = FALSE)
    {
        $CI =& get_instance();

        $prefix = '';

        if ( ! isset($_COOKIE[$index]) && config_item('cookie_prefix') != '')
        {
            $prefix = config_item('cookie_prefix');
        }

        return $CI->input->cookie($prefix.$index, $xss_clean);
    }
    
    /**
     * 返回所有cookie
     *
     * @access  public
     * @param   void
     * @return  array
     */
    function get_all()
    {
        return $_COOKIE;
    }
    
    /**
     * Delete a COOKIE
     *
     * @param   mixed
     * @param   string  the cookie domain.  Usually:  .yourdomain.com
     * @param   string  the cookie path
     * @param   string  the cookie prefix
     * @return  void
     */
    function del($name = '', $domain = '', $path = '/', $prefix = '')
    {
        if(is_array($name)) // 如果第一个参数是数组的话
        {
            foreach($name as $key => $val)
            {
                $this->set($key, '', '', $domain, $path, $prefix);
            }
        }
        else
        {
            $this->set($name, '', '', $domain, $path, $prefix);
        }
    }
    
    /**
     * destroy a COOKIE
     *
     * @param   mixed
     * @param   string  the cookie domain.  Usually:  .yourdomain.com
     * @param   string  the cookie path
     * @param   string  the cookie prefix
     * @return  void
     */
    function destroy()
    {
        
    }
}