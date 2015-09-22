    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                        	
                        	<div class="ibox-title">
                        		<h3><?php echo @$_GET['title']?></h3>
                        		<a href="?id=<?php echo @$_GET['id']?>&title=<?php echo @$_GET['title']?>" class="btn btn-xs <?php echo !isset($_GET['good'])?'btn-primary':'btn-default';?>">所有记录</a>  
                        		<a href="?id=<?php echo @$_GET['id']?>&title=<?php echo @$_GET['title']?>&good=1" class="btn btn-xs  <?php echo isset($_GET['good'])?'btn-primary':'btn-default';?>">只看神评</a>
                            </div>
                            
                            <div class="ibox-content">
<!-- 搜索 s--> 
                    <div class="input-group">
                             <input class="form-control" placeholder="请输入用户昵称" type="text"> <span class="input-group-btn"> <button type="button" class="btn btn-primary">搜索
                                        </button> </span>
                                            </div>
<!-- 搜索 e-->                                             
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>信息ID</th>
                                            <th>聊天时间</th>
                                            <th>用户</th>
                                            <th>消息内容</th>
                                            <th>点赞数<span class="text-danger">（修改后即可生效）</span></th>
                                            <th>管理操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="center"><?php echo time_format($v['chattime']);?></td>
                                            <td class="center"><?php echo $v['account'];?></td>
                                            <td class="center"><?php 
                                            switch($v['content_type']){
												case '1' : echo $v['content'];  //文字
													break;
												case '2' :
					 echo "<a class='big_img'><img height=60 src='".$v['url']."' data-toggle='modal' data-target='#myModal' title='单击查看' /></a>";  //图片
													break;
												case '3' :
					 echo "语音:".$v['url'];  //语音
													break;
												case '4' :
					 echo "<h3><a class='big_video' key='".$v['url']."' data-toggle='modal' data-target='#myModal'><i class='fa fa-video-camera text-success'> 点击查看视频</i></a></h3>";//视频
													break;
												default :
													echo '格式有误';
													break;																								
                                            }
                                            ?>
                                            </td>
                                            <td class="center">
                                            	<input class="hx_msgid" type="hidden" value="<?php echo $v['hx_msgid'];?>">
                                            	<input class="form-control zan" type="text" value="<?php echo $v['zan_num'];?>">
                                            </td>
                                            <td class="center"><a href="/admin/show/del_chatlogs/<?php echo $v['hx_msgid'];?>">删除</a></td>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">聊天媒体文件</h4>
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
		var img = '<img src="'+src+'" />' 
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


$(".zan").blur(function(e){
	var hx_msgid = $(this).parent().find('.hx_msgid').val();
	var val = $(this).val();
	$(this).keyup(function(){
		
	});
	var url = '/admin/show/zan_edit/'+hx_msgid+'/'+val;
	if(!isNaN(val)){
		$.get(url,{},function(data){
			if(data!=1){
				alert('程序出错，请联系管理员！');
			}
		})
	}else{
		alert('请输入整数！');
	}

});
});
</script>               