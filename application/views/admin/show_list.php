    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                        	
                        	<div class="ibox-title">
                        		 <a href="/admin/show/show_add?type=2" class="btn btn-primary">新建节目</a>
                            </div>
                            
                            <div class="ibox-content">
<!-- 搜索 s--> 
                    <div class="input-group col-sm-6">
                    	<form action="/admin/show/showlist" method="get">
                             <input class="form-control" name="title" value="<?php echo @$_GET['title']?>" placeholder="请输入节目ID/名称" type="text">
							<span class="input-group-btn"> <button type="submit" class="btn btn-primary">搜索</button> </span>
                        </form>
                    </div>
<!-- 搜索 e-->                                             
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>类型</th>
                                            <th>封面</th>
                                            <th>内容</th>
                                            <th>开始时间</th>
                                            <th>结束时间</th>
                                            <th>状态</th>
                                            <th>主播</th>
                                            <th>在线人数</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($show_list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td><?php echo $v['show_type'];?></td>
                                            <td class="center">
                                            	<?php if($v['file_type']==0)
                                            	echo "<a class='big_img'><img height=60 src='".$v['banner_url']."' data-toggle='modal' data-target='#vModal' title='单击查看' /></a>";
													else
												echo "<a class='big_video' key='".$v['banner_url']."' data-toggle='modal' data-target='#vModal' title='单击查看'><i class='fa fa-video-camera text-success'> 查看视频</i></a>";		
                                            	?>
                                            </td>
                                            <td class="center"><?php echo $v['title'];?></td>
                                            <td class="center"><?php echo time_format($v['start_time']);?></td>
                                            <td class="center"><?php echo time_format($v['end_time']);?></td>
                                            <td><?php if($v['status']==0){echo '<span class="label">隐藏';}else{echo '<span class="label label-success">显示';}?></span>
                                            	<?php if($v['end_time']<time()-15*60){echo '<span class="label label-danger">已结束';}else if($v['start_time']<=time()+45*60 && $v['end_time']>=time()-15*60){echo '<span class="label label-success">进行中';}else{echo '<span class="label label-primary">未开始';} ?></span></td>
                                            <td><?php echo $v['zhubo'][0]['nick_name'];if($v['zhubo2']) echo " | ".$v['zhubo'][1]['nick_name'];?></td>
                                            <td><?php echo $v['audience_num'];?></td>
                                            <td><div class="btn-group">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">操作 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="/admin/show/robot_talk/<?php echo $v['id']."?title=".$v['title'];?>"  class="font-bold">马甲发言</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/show/robot_add/<?php echo $v['id']."?title=".$v['title'];?>"  class="font-bold">增加马甲</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/show/show_add?id=<?php echo $v['id'];?>" class="font-bold">修改</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/show/true_showlogs?id=<?php echo $v['id']."&title=".$v['title'];?>" class="font-bold">真实用户数</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/admin/show/show_chatlogs?id=<?php echo $v['id']."&title=".$v['title'];?>" class="font-bold">聊天记录/神评</a></li>
                                            <li class="divider"></li>                                            
                                            <li><a href="/admin/show/delshowbyid/<?php echo $v['id'];?>" class="font-bold">删除</a>
                                            </li>
                                        </ul>
                                    </div></td>
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

<!-- 弹窗s -->
<div class="modal fade" id="vModal" tabindex="-1" role="dialog" aria-labelledby="vModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">节目封面</h4>
                </div>
                <div class="modal-body">
                	<!-- 插入当前媒体文件  -->
					<div class="form-group" id="big_div">
						
				    </div>   
				                </div>
				            </div>
				        </div>
				    </div>
<!-- 弹窗e --> 
<script type="text/javascript">
$(function(){
	//图片
	$('.big_img').click(function(){
		var src = $(this).find('img').attr('src');
		var img = '<img style="max-width:550px" src="'+src+'" />' 
		$('#big_div').html(img);
	});
	//视频
	$('.big_video').click(function(){
		var src = $(this).attr('key');
        var v = '<video width="320" height="240" controls="controls" autoplay="autoplay">';
			v += '<source src="'+src+'" type="video/mp4" />';
			v += '</video>';
		$('#big_div').html(v);	
	});
});
</script>                              
