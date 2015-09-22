    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            
                            <div class="ibox-title">
                              <a href="/admin/weibo/add" class="btn btn-primary">发布微博</a>
                            </div>
                            <div class="ibox-content">
                                
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            
                                            <th>ID</th>
                                            <th>账号</th>
                                            <th>内容</th>
                                            <th>评论数</th>
                                            <th>点赞数</th>
                                            <th>浏览次数</th>
                                            <th>收藏次数</th>
                                            <th>转发时间</th>
                                            <th>微博IP</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($weibo_list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['w_id'];?></td>
                                            <td class="title"><?php echo $v['account'];?></td>
                                            <td class="center"><?php echo $v['content'];?></td>
                                            <td class="center"><?php echo $v['comment_num']; ?></td>
                                            <td class="center"><?php echo $v['zan_num']; ?></td>
                                            <td class="center"><?php echo $v['view_num']; ?></td>
                                            <td class="center"><?php echo $v['collect_num']; ?></td>
                                            <td class="center">
                                                <?php echo time_format($v['w_time']);?>
                                            </td>
                                            <td class="center">
                                                <?php echo long2ip($v['ip']);?>
                                            </td>
                                            <td>
                                                <?php
                                                if($v['w_status'] == 1){
                                                    echo '可见';
                                                } else {
                                                    echo '已删除';
                                                };
                                                ?>
                                            </td>
                                            <td>
                                                
                                                    <div class="btn-group">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">操作 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:void(0);"  name="del_click" bid="<?php echo $v['w_id']; ?>" enabled="0">删除</a></li>
                                            <li class="divider"></li>               
                                            <li><a href="/admin/weibo/edit?w_id=<?php echo $v['w_id']; ?>" >修改</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/weibo/view_comment?w_id=<?php echo $v['w_id']; ?>" >查看评论</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/weibo/add_comment?w_id=<?php echo $v['w_id']; ?>" >增加评论</a></li>
                                            
                                            <li class="divider"></li>
                                            <li><a href="/admin/weibo/view_zan?w_id=<?php echo $v['w_id']; ?>" >查看点赞</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/weibo/view_collect?w_id=<?php echo $v['w_id']; ?>" >查看收藏</a></li>
                                        </ul>
                                    </div>
                                                
                                            </td>
                                        </tr>
                   <?php }?>                     
                                       
                                    </tbody>
                                </table>
<!-- 页码 s-->                                
					<div class="row"><?php echo $page;?></div>
<!-- 页码 e--> 
							</div>
                        </div>
                    </div>
                </div>

     
    

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script type="text/javascript">

    
$(function() {
        
        //删除
        $('a[name="del_click"]').click(function(){
            var data = {
                wid : $(this).attr('bid')
            };
            var url = '/admin/weibo/delweibo/'
            $.post(url, data, function(result){
                if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href =  '/admin/weibo/getweibolist/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
            }, 'json');
        });
})

        
        
</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/adddate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script>  