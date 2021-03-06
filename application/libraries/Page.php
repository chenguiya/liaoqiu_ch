<?php
/**
 * 分页类
 * @author  xiaojiong & 290747680@qq.com
 * @date 2011-08-17
 *
 * show(2)  1 ... 62 63 64 65 66 67 68 ... 150
 * 分页样式
 * #page{font:12px/16px arial}
 * #page span{float:left;margin:0px 3px;}
 * #page a{float:left;margin:0 3px;border:1px solid #ddd;padding:3px 7px; text-decoration:none;color:#666}
 * #page a.now_page,#page a:hover{color:#fff;background:#05c}
 *
 * 2014-11-04 zhangjh 复制自 http://www.oschina.net/code/snippet_162279_5852
 * m1 2014-11-17 zhangjh 增加文章分页，用文章内容上的“[page]”分割
 */

class Page
{
    public      $first_row;             //起始行数
    public      $list_rows;             //列表每页显示行数
    protected   $total_pages;           //总页数
    protected   $total_rows;            //总行数
    protected   $now_page;              //当前页数
    protected   $method  = 'defalut';   //处理情况 Ajax分页 Html分页(静态化时) 普通get方式
    protected   $parameter = '';
    protected   $page_name;             //分页参数的名称
    protected   $ajax_func_name;
    public      $plus = 3;              //分页偏移量
    protected   $url;
    protected   $begin;
    protected   $end;

    /**
     * 构造函数
     * @param unknown_type $data
     */
    public function __construct($data = array())
    {
        if(count($data) > 0)
        {
            $this->total_rows = $data['total_rows'];

            $this->parameter        = !empty($data['parameter']) ? $data['parameter'] : '';
            $this->list_rows        = !empty($data['list_rows']) && $data['list_rows'] <= 100 ? $data['list_rows'] : 20;
            $this->total_pages      = ceil($this->total_rows / $this->list_rows);
            $this->page_name        = !empty($data['page_name']) ? $data['page_name'] : 'p';
            $this->ajax_func_name   = !empty($data['ajax_func_name']) ? $data['ajax_func_name'] : '';

            $this->method           = !empty($data['method']) ? $data['method'] : '';

            /* 当前页面 */
            if(!empty($data['now_page']))
            {
                $this->now_page = intval($data['now_page']);
            }
            else
            {
                $this->now_page = !empty($_GET[$this->page_name]) ? intval($_GET[$this->page_name]):1;
            }
            $this->now_page = $this->now_page <= 0 ? 1 : $this->now_page;


            if(!empty($this->total_pages) && $this->now_page > $this->total_pages)
            {
                $this->now_page = $this->total_pages;
            }
            $this->first_row = $this->list_rows * ($this->now_page - 1);
        }
    }

    /**
     * 得到当前连接
     * @param $page
     * @param $text
     * @return string
     */
    protected function _get_link($page,$text,$params = array(),$class = '')
    {
        switch ($this->method) {
            case 'ajax':
                $parameter = '';
                if($this->parameter)
                {
                    $parameter = "{$this->parameter}";
                }
            return '<a data-page="'.$page.'"'.$parameter.' href="javascript:void(0)">' . $text . '</a>' . "\n";
            break;

            case 'html':
                $url = str_replace('?', $page,$this->parameter);
				if(!empty($_SERVER['QUERY_STRING'])) $url .= "?".$_SERVER['QUERY_STRING'];
                return '<li tabindex="0" aria-controls="DataTables_Table_0" class="paginate_button'.$class.'"><a href="' .$url . '">' . $text . '</a></li>' . "\n";
            break;

            default:
                return '<a href="' . $this->_get_url($page) . '">' . $text . '</a>' . "\n";
            break;
        }
    }

    /**
     * 设置当前页面链接
     */
    protected function _set_url()
    {
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$this->page_name]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        if(!empty($params))
        {
            $url .= '&';
        }
        $this->url = $url;
    }

    /**
     * 得到$page的url
     * @param $page 页面
     * @return string
     */
    protected function _get_url($page)
    {
        if($this->url === NULL)
        {
            $this->_set_url();
        }
    //  $lable = strpos('&', $this->url) === FALSE ? '' : '&';
        return $this->url . $this->page_name . '=' . $page;
    }


    /**
     * 得到第一页
     * @return string
     */
    public function first_page($name = '第一页')
    {
        if($this->begin > 1)
            return $this->_get_link('1', $name).($this->begin - 1 > 1 ? '<li><a>...</a></li>' : '');
    }

    /**
     * 最后一页
     * @param $name
     * @return string
     */
    public function last_page($name = '最后一页')
    {
        if($this->total_pages > $this->end)
            return ($this->total_pages - $this->end > 1 ? '<li><a>...</a></li>' : '').$this->_get_link($this->total_pages, $name);
    }

    /**
     * 上一页
     * @return string
     */
    public function up_page($name = '上一页', $class = '')
    {
        if($this->now_page != 1)
        {
            return $this->_get_link($this->now_page - 1, $name, $class);
        }
        return '';
    }

    /**
     * 下一页
     * @return string
     */
    public function down_page($name = '下一页', $class = '')
    {
        if($this->now_page < $this->total_pages)
        {
            return $this->_get_link($this->now_page + 1, $name, $class);
        }
        return '';
    }

    /**
     * 分页样式输出
     * @param $param
     * @return string
     */
    public function show($param = 1)
    {
        if($this->total_rows < 1)
        {
            return '';
        }

        $className = 'show_' . $param;

        $classNames = get_class_methods($this);

        if(in_array($className, $classNames))
        {
            return $this->$className();
        }
        return '';
    }

    protected function show_2()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            $return .= $this->up_page('<');
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<a class='now_page'>$i</a>\n";
                }
                else
                {
                    if($this->now_page-$i>=4 && $i != 1)
                    {
                        $return .="<span class='pageMore'>...</span>\n";
                        $i = $this->now_page-3;
                    }
                    else
                    {
                        if($i >= $this->now_page+5 && $i != $this->total_pages)
                        {
                            $return .="<span>...</span>\n";
                            $i = $this->total_pages;
                        }
                        $return .= $this->_get_link($i, $i) . "\n";
                    }
                }
            }
            $return .= $this->down_page('>');
            return $return;
        }
    }

    protected function show_1()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }

        $begin = ($begin >= 1) ? $begin : 1;
        $return = '';
        $return .= $this->first_page();
        $return .= $this->up_page();
        for ($i = $begin; $i <= $begin + $plus * 2;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= "<a class='now_page'>$i</a>\n";
            }
            else
            {
                $return .= $this->_get_link($i, $i) . "\n";
            }
        }
        $return .= $this->down_page();
        $return .= $this->last_page();
        return $return;
    }

    protected function show_3()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '总计 ' .$this->total_rows. ' 个记录分为 ' .$this->total_pages. ' 页, 当前第 ' . $this->now_page . ' 页 ';
        $return .= ',每页 ';
        $return .= '<input type="text" value="'.$this->list_rows.'" id="pageSize" size="3"> ';
        $return .= $this->first_page()."\n";
        $return .= $this->up_page()."\n";
        $return .= $this->down_page()."\n";
        $return .= $this->last_page()."\n";
        $return .= '<select onchange="'.$this->ajax_func_name.'(this.value)" id="gotoPage">';

        for ($i = $begin;$i<=$begin+10;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= '<option selected="true" value="'.$i.'">'.$i.'</option>';
            }
            else
            {
                $return .= '<option value="' .$i. '">' .$i. '</option>';
            }
        }
         $return .= '</select>';
        return $return;
    }

    // m1
    protected function show_4()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }

        $begin = ($begin >= 1) ? $begin : 1;
        
				
        $return = '<div class="col-sm-6"><div aria-relevant="all" aria-live="polite" role="alert" id="DataTables_Table_0_info" class="dataTables_info">';
		$return .= '总计  ' .$this->total_rows. ' 个记录，分为 ' .$this->total_pages. ' 页, 当前第 ' . $this->now_page . ' 页 ';
        $return .= ',每页 '.$this->list_rows." 项";
		$return .='</div></div>';
		$return .='<div class="col-sm-6">';
		$return .='<div id="DataTables_Table_0_paginate" class="dataTables_paginate paging_simple_numbers">';
		$return .='<ul class="pagination">';
        $return .= $this->up_page('上一页', ' class="prenxt"');
        $this->begin = $begin;
        $this->end = $begin + $plus * 2;
        $return .= $this->first_page(1);
        for ($i = $begin; $i <= $begin + $plus * 2 ;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= '<li tabindex="0" aria-controls="DataTables_Table_0" class="paginate_button active"><a>'.$i.'</a></li>';
            }
            else
            {
                $return .= $this->_get_link($i, $i) . "";
            }
        }
        $return .= $this->last_page($this->total_pages);

        $return .= $this->down_page('下一页', ' class="prenxt"');
        $return .='</ul></div></div>';
        return $return;
    }

    protected function show_5(){
    	$plus = $this->plus;
    	if( $plus + $this->now_page > $this->total_pages) {
    		$begin = $this->total_pages - $plus * 2;
    	} else {
    		$begin = $this->now_page - $plus;
    	}

    	$begin = ($begin >= 1) ? $begin : 1;
    	$return = '<div id="pages" class="pages">';

    	$return .= $this->up_page('上一页', ' class="prenxt"');
    	$this->begin = $begin;
    	$this->end = $begin + $plus * 2;
    	$return .= $this->first_page(1);


    	for ($i = $begin; $i <= $begin + $plus * 2 ;$i++){
	    	if($i>$this->total_pages)	{
	    		break;
	    	}
	    	if($i == $this->now_page){
	    			$return .= "<span>$i</span> ";
	    	}else{
	    		$return .= $this->_get_link($i, $i) . "";
	    	}
    	}
    	$return .= $this->last_page($this->total_pages);

    	$return .= $this->down_page('下一页', ' class="prenxt"');

    	$return .= '</div>';
    	return $return;
    }

}

/**
    在CI的Model中 eg.
    echo "<style>page{font:12px/16px arial}
        page span{float:left;margin:0px 3px;}
        page a{float:left;margin:0 3px;border:1px solid #ddd;padding:3px 7px; text-decoration:none;color:#666}
        page a.now_page,#page a:hover{color:#fff;background:#05c}
        </style>";

    // 1、默认情况
    $_GET['p'] = 1;
    $params = array(
        'total_rows'=>100, #(必须)
        'now_page'  =>$_GET['p'],  #(必须)
        'list_rows' =>10, #(可选) 默认为15
    );
    $this->load->library('page', $params);
    echo '<div id="page">'.$this->page->show(1).'</div>';

    // 2、处理html静态化页面分页的情况
    # method 处理环境 设置为 html
    # parameter 为静态页面参数  xxx.com/20-0-0-0-40-?.html 注意问号
    # ?问号的位置会自动替换为去向页码
    # now_page 当前页面(静态页面获取不到当前页面所以只有你传入)
    $_GET['p'] = 1;
    $params = array(
        'total_rows'=>100, #(必须)
        'method'    =>'html', #(必须)
        'parameter' =>'xxx.com/20-0-0-0-40-?.html',  #(必须)
        'now_page'  =>$_GET['p'],  #(必须)
        'list_rows' =>10, #(可选) 默认为15
    );
    $this->load->library('page', $params);
    echo '<div id="page">'.$this->page->show(1).'</div>';
    echo "<br>";

    // 3、处理ajax分页的情况
    # method 处理环境 设置为 ajax
    # ajax_func_name ajax分页跳转页面的javascript方法
    # parameter    ajax_func_name后面的附带参数 默认为空
    # now_page 不到当前页面所以只有你传入
    $params = array(
        'total_rows'=>100,
        'method'    =>'ajax',
        'ajax_func_name' =>'goToPage',
        'now_page'  =>1,
        #'parameter' =>"'jiong','username'",
    );
    $this->load->library('page', $params);
    echo '<div id="page">'.$this->page->show(1).'</div>';
    #<a href="javascript:void(0)" onclick="goToPage('7')">7</a>
    #添加了parameter<a href="javascript:void(0)" onclick="goToPage('6','jiong','username')">6</a>
 */