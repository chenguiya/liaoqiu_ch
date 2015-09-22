<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 查文件夹下有什么文件
function route_file_list($dir)
{
    if ($dir[strlen($dir)-1] != DS) $dir .= DS;
    if (!is_dir($dir)) return array();

    $dir_handle  = opendir($dir);
    $dir_objects = array();

    while ($object = readdir($dir_handle))
    {
        if (!in_array($object, array('.','..')))
        {
            $filename = $dir . $object;
            $dir_objects[] = $object;
        }
    }
    return $dir_objects;
}

// ============================== 批量route设置开始 ==============================
$route_dirname = dirname(__FILE__);

// controllers 后期加上
$route_controllers_dir = $route_dirname.DS.'..'.DS.'controllers';
$arr_route_controllers = route_file_list($route_controllers_dir);

// views 给前端测试用
$route_views_dir = $route_dirname.DS.'..'.DS.'views';
$arr_route_views = route_file_list($route_views_dir);
foreach($arr_route_views as $k => $v)
{
    $_tmp_arr = explode('.', $v);
    if(@$_tmp_arr[1] == 'php')
    {
        $route[$_tmp_arr[0]] = 'pages/view/'.$_tmp_arr[0];
    }
}

// passport 的批量路由
$arr_route_views = route_file_list($route_views_dir.DS.'passport');
foreach($arr_route_views as $k => $v)
{
    $_tmp_arr = explode('.', $v);
    if(@$_tmp_arr[1] == 'php')
    {
        $route['passport1/'.$_tmp_arr[0]] = 'passport1/view/'.$_tmp_arr[0];
    }
    $route['passport1'] = 'passport1/view/index';
}
$route['passport/system_notice/([0-9]+)'] = 'passport/system_notice/index/$1';
$route['passport/collection/([0-9]+)'] = 'passport/collection/index/$1';
$route['passport'] = 'passport/profile';
$route['passport/(:any)'] = 'passport/$1';

// ============================== 批量route设置结束 ==============================

// 会员
$route['member/(:any)'] = 'member/$1';

// nba视频
$route['lx/tag/(:any)-(\d+)'] = 'nba_video/tag/$1/$2';
$route['lx/tag/(:any)'] = 'nba_video/tag/$1';
$route['lx/matchs/(\d+-\d+-\d+)'] = 'nba_video/events/$1';
$route['lx/([a-z0-9]+)/(\d+)'] = 'video/show/$1/$2';
$route['lx/([a-z0-9]+)-(\d+)'] = 'nba_video/lists/$1/$2';
$route['lx/([a-z0-9]+)'] = 'nba_video/lists/$1';
$route['lx'] = 'nba_video/index';
$route['lx/(:any)'] = 'nba_video/lists/$1';                

// 视频
$route['video/([a-z0-9]+)_(\d+)'] = 'video/lists/$1/$2';
$route['video/([a-z0-9]+)/(\d+)'] = 'video/show/$1/$2';
$route['video'] = 'video/index';
$route['video/(:any)'] = 'video/lists/$1';                    // 频道子频道首页

// 花边
$route['gossip/([a-z0-9]+)_(\d+)'] = 'gossip/lists/$1/$2';         // 频道列表页分页
$route['gossip/([a-z0-9]+)/(\d+)'] = 'gossip/show/$1/$2';          // 频道详细页
$route['gossip/([a-z0-9]+)/(\d+)_(\d+)'] = 'gossip/show/$1/$2/$3'; // 频道详细页分页
$route['gossip/index'] = 'gossip/index';                        // 频道首页
$route['gossip/(:any)'] = 'gossip/lists/$1';                    // 频道子频道首页

// 资讯
$route['news/([a-z0-9]+)_(\d+)'] = 'news/lists/$1/$2';
$route['news/([a-z0-9]+)/(\d+)'] = 'news/show/$1/$2';
$route['news/([a-z0-9]+)/(\d+)_(\d+)'] = 'news/show/$1/$2/$3';
$route['news/index'] = 'news/index';
$route['news/(:any)'] = 'news/lists/$1';

//$route['special/'] = 'special/index';
// 观点
$route['view'] = 'view/index';
$route['view/expert/([0-9]+)'] = 'view/expert_show/$1';
$route['view/expert'] = 'view/expert_lists';
$route['view/([a-z0-9]+)_(\d+)'] = 'view/lists/$1/$2';
$route['view/([a-z0-9]+)/(\d+)'] = 'view/show/$1/$2';
$route['view/([a-z0-9]+)/(\d+)_(\d+)'] = 'view/show/$1/$2/$3';
$route['view/index'] = 'view/index';
$route['view/(:any)'] = 'view/lists/$1';


// 深度

//赛程中心
$route['sc'] = 'event/index';
$route['sc/(\d+)'] = 'event/index/$1';

// 图库
$route['pic/([a-z0-9]+)_(\d+)'] = 'pic/lists/$1/$2';
$route['pic/([a-z0-9]+)/(\d+)'] = 'pic/show/$1/$2';
$route['pic/([a-z0-9]+)/(\d+)_(\d+)'] = 'pic/show/$1/$2/$3';
$route['pic/index'] = 'pic/index';
$route['pic/star-view/(\d+)'] = 'pic/show/star-view/$1';
$route['pic/(:any)'] = 'pic/lists/$1';

//深度
$route['special/([a-z0-9]+)'] = 'special/lists/$1/1';
$route['special/([a-z0-9]+)/(\d+)'] = 'special/lists/$1/$2';
$route['special'] = 'special/index';

//独家
$route['dujia/(\d+)'] = 'dujia/index/$1';
$route['dujia'] = 'dujia/index';
//滚动
$route['scroll/(\d+)'] = 'scroll/index/$1';
$route['scroll'] = 'scroll/index';

// AJAX
$route['ajax/(:any)'] = 'ajax/$1';

// ERROR
$route['error/(:any)'] = 'error/show/$1';

$route['rss'] ='response/init/rss';
$route['rss/(:any)'] ='response/init/rss/$1';

// $route['(:any)'] = 'pages/view/$1';
$route['default_controller'] = 'pages/view/home';           // 首页
$route['404_override'] = 'error/show/404';

/* End of file routes.php */
/* Location: ./application/config/routes.php */