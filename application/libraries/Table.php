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

class Table
{
  function market_sig($n,$o) {

     if($o==''){$o=0;}

	  $result = $this->db->query ( "select * from market_sig where del!=0 order by sig_time desc limit $o,$n" );
	
	  $re = $result->result ();
	
	  return $re;

 }
}