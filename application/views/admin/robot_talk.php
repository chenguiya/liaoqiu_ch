    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"> 
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">                                          
<!--表单s -->
					<div class="col-lg-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php echo @$_GET['title']?></h5>
                            </div>
                            <div class="ibox-content">
				<form method="post" class="form-horizontal" enctype="multipart/form-data">
					<input type="hidden" name="type" id="type" value="2">
				                                	<div class="form-group">
				                                        <label class="col-sm-2 control-label">在线用户</label>
				                                        <div class="col-sm-10">
				                                           <select id="member_id" name="member_id" class="autosubmit"><option>正在加载...</option></select>
				                                            <button id="choose" type="button" class="btn btn-xs btn-success">随机用户</button>
				                                        </div>
				                                    </div>
				                                    <div class="form-group">
				                                        <label class="col-sm-2 control-label">节目ID</label>
				                                        <div class="col-sm-10">
				                                        	<input type="text" class="form-control" id="show_id" name="show_id" value="<?php echo $id;?>" disabled="" size="10" />
				                                        </div>
				                                    </div>                                  
				                                     <div class="form-group">
				                                        <label class="col-sm-2 control-label">内容</label>
				                                        <div class="col-sm-10">
				                                        	<input type="text" id="msg" name="msg" size="80" class="form-control">
				                                        </div>
				                                    </div>                                  
				                                      <div class="form-group">
				                                        <label class="col-sm-2 control-label">话题</label>
				                                        <div class="col-sm-10">
				                                        	<input type="text" id="topic" name="topic" size="80" class="form-control">
				                                        </div>
				                                    </div>                                  
				                                                                                                                                                                   
				                                    <div class="form-group">
				                                        <div class="col-sm-4 col-sm-offset-2">
				                                        	<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal">图文消息</button>
				                                            <button class="btn btn-primary" id="dosubmit" type="button">发送</button>
				                                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
				                                        </div>
				                                    </div>
				                                </form>
                            </div>
                        </div>
                    </div>
<!--表单e -->
							</div>
                        </div>
                    </div>
                </div>


<!-- 弹窗 s-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">图片消息</h4>
                </div>
                <div class="modal-body">
				<form method="post" class="form-horizontal" action="http://www.lq.com/robot/sendimg?sign=bee1fe643644eb0d8d3641bf0ff6d560" enctype="multipart/form-data">
                                	<div class="form-group">
                                       <input type='file' name="file" id="file" onchange="previewImage(this)" />
                                    </div>
						
					<div id="send-file-warning"></div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"></label>
                                        <div class="col-sm-10">
                                        	<div id="preview"></div>
                                        </div>
                                    </div>                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                        	<button type="button" id="imgsubmit" class="btn btn-primary" >发送</button>
                                            <button type="button" id="cancelfileSend" class="btn btn-white" data-dismiss="modal">取消</button>
                                        </div>
                                    </div>
                                </form>
                </div>
            </div>
        </div>
    </div>
<!-- 弹窗 e-->

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script type="text/javascript">
$(function(){
	choose($("#show_id").val());
	setInterval(function(){
    		choose($("#show_id").val()); //两分钟更新一次
	}, 120100);
	function choose(id){
		var url = '<?php echo $this->config->item('domain')?>/robot/ajax_return_showlogs?show_id='+id;
		var key = '<?php echo $this->config->item('api_key')?>';
		var sign = $.md5(encodeURIComponent(url)+key);
		$.getJSON(url,{sign:sign},function(data){
			if(data != ''){
				var html = '';
				 $.each(data, function(k){
				 	html += '<option value="'+data[k].member_id+'">'+data[k].account+'</option>';
	   			 });
			
			}else{
				html += '<option>暂无数据</option>';
			}
			 $("#member_id").html(html);
		});
		
	}
	
	$('#choose').click(function(){
		var l = $("#member_id option").length;
		var num = getRandom(l);
		var val = $("#member_id option:eq("+num+")").val();
		$("#member_id").val(val);
		$('#member_id option').each(function(){ 
			if( $(this).val() == val){ 
				var name = $(this).html();
				var html = '<option value="'+val+'">'+name+'</option>';
				$('#member_id').prepend(html);
				$(this).remove();  //删除重复项
				return false;  //终止
			} 
		}); 
    });
    
    
	$('#dosubmit').click(function(){
		var member_id = $('#member_id').val();
		var id = $('#show_id').val();
		var msg = $('#msg').val();
		var topic = $('#topic').val();
		var url = "<?php echo $this->config->item('domain')?>/robot/sendmsgbyshowid?id="+id+"&member_id="+member_id+"&msg="+msg+"&topic="+topic;
		var key = '<?php echo $this->config->item('api_key')?>';
		var sign = $.md5(encodeURIComponent(url)+key);
		if(msg){
			$.get(url,{sign:sign},function(data){
				if(data){
					alert(data);
				}
			})
		}else{
			alert('内容不能为空！');
		}
		
    });
 
     
    
	$('#imgsubmit').click(function(){
		var member_id = $('#member_id').val();
		var id = $('#show_id').val();
		var filename = $('#filename').val();
		var secret = $('#secret').val();
		var uri = $('#url').val();
		var topic = $('#topic').val();
		var url = "<?php echo $this->config->item('domain')?>/robot/sendmsgbyshowid?filename="+filename+"&id="+id+"&img="+secret+"&member_id="+member_id+"&topic="+topic+"&url="+uri;
		var key = '<?php echo $this->config->item('api_key')?>';
		var sign = $.md5(encodeURIComponent(url)+key);
		if(msg){
			$.get(url,{sign:sign},function(data){
				if(data){
					alert(data);
				}
			})
		}else{
			alert('内容不能为空！');
		}
		
    });
    
    function getRandom(n){
        return Math.floor(Math.random()*n+1)
    }
    
    //上传图片
    $("#file").wrap("<form id='myupload' action='/admin/show/uploadimg' method='post' enctype='multipart/form-data'></form>");
	$("#file").change(function(){
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			success: function(data) {
				if(data.code==0){
		            var html ='<input id="secret" type="hidden" value="'+data.secret+'" />';
		                html +='<input id="url" type="hidden" value="'+data.url+'" />';
		                html +='<input id="filename" type="hidden" value="'+data.filename+'" />';
		            $("#preview").append(html);
				}else{
					alert('操作失败，请联系管理员！');
				}

			}
		});
	});
 
});
</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script>  