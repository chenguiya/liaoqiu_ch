<?php

/* *
 *  工具模块
 * 
 */
require_once (__DIR__ . "/Base.php");
class Tools extends Base
{
    
    public function __construct() 
    {
        parent::__construct();     
    }
    
    public function index()
    {
        echo '测试';
        
    }

    /**
     * 同步会员头像到新的路径下
     * 
     */
    public function synchronous_member_face()
    {
        set_time_limit(0);
        $this->load->helper('huanxin_helper');
        $count = $this->get_members_count();
        $fails = array();
        $start = 0;
        $limit = 300;
        $childs = array();
        while (true)
        {
            $members = $this->get_members($start, $limit);
            
            if(empty($members) )
            {
                break;
            }
            
            foreach ($members as $member)
            {
                
                $member_detail = getinfo_byuserid($member['member_id']);
                $member_id = $member['member_id'];
                
                if(!empty($member_detail) && empty($member_detail['state_code']) && !empty($member_detail['user_detail']))
                {
                    $logo = $member_detail['user_detail'][0]['logo'];
                    //内网测试用
                    if(get_config_field('5u_environment') == 'development')
                    { 
                        $logo = 'http://www.5usport.com/data/attachment/forum/201508/28/082302lqnuzucynu2gyo42.png';  
                    }
                    //跳过默认图片
                    if(strpos($logo, 'noavatar_big.gif') == FALSE)
                    {
                        //下载图片
                        $content = $this->download_image($logo);
                        //失败
                        if(!empty($content))
                        {             
                            //文件类型
                            $type = $this->get_file_type($logo);
                            $save_result = $this->save_image_file($member_id, $type, $content);
                            //创建头像
                            $result = new_passport_avatar_create($member_id, $this->get_temp_filename($member_id, $type), $type);
                            if(empty($save_result) || empty($result))
                            {
                                $fails[] = $member_id;
                                echo '失败：', $member_id;
                            }
                        }   

                    }
                }
                    
            }
            
            $start += $limit;
            
        }
        
        if(!empty($fails))
        {
            $count = count($fails);
            echo '失败：';
            echo '<pre>';
            echo 'count:', $count;
            print_r($fails);
            echo '</pre>';   
        }
        else 
        {
            echo '成功！';
            
        }
        
        exit;  
    }
    
    /**
     * 获取用户的临时文件
     * 
     * @param int $member_id
     * @param string $type
     */
    private function get_temp_filename($member_id, $type)
    {
        return get_config_field('avatar_api_dir') . 'temp_' . $member_id . '.' . $type;
    }


    private function save_image_file($member_id, $type, $content)
    {
        //保存图片
        $temp_filename = $this->get_temp_filename($member_id, $type);
        $temp_handler = @fopen($temp_filename, 'w+');
        $return = fwrite($temp_handler, $content);
        fclose($temp_handler);
        return $return === false ? false : true;
    }

    /**
     * 根据URL 分析文件的格式
     * 
     * @param string $url
     */
    private function get_file_type($url)
    {
        $parts = parse_url($url);
        $path_extension = '';
        
        if(!empty($parts['path']))
        {
            $path =  $parts['path'] ; 
            $path_extension = pathinfo($path, PATHINFO_EXTENSION);
        }
        
        return $path_extension;
    }

    /**
     * 保存用户头像
     * 
     * @param int $member_id
     * @param binary $content
     */
    private function save_image($member_id, $content, $type)
    {
        
    }

    
    /**
     *远程下载图片
     *  
     * @param string $url
     */
    private function download_image($url)
    {
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 7.0;Windows NT 5.2)';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        $result = curl_exec($curl);
        $err = curl_errno($curl);
        curl_close($curl);
        
        if($err)
        {
            echo '抓取URL:', $url,'。出错';
            return FALSE;
        }
        return $result;
    }

    /**
     * 获取会员列表
     * 
     * @param int $start
     * @param int $limit
     * @return array
     */
    private function get_members($start = 0, $limit = 1000)
    {
        load_model('member');
        $params = array(
            'start' => $start,
            'limit' => $limit,
        );
        return $this->liaoqiu_member_model->getMemberList($params);
    }
    
    /**
     * 获取会员总数
     * 
     * @return int
     */
    private function get_members_count()
    {
        return $this->db->count_all('member');
    }
}