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
                                <h5>新增菜单</h5>
                            </div>
                            <div class="ibox-content">
                                <form novalidate="novalidate" class="form-horizontal m-t" id="commentForm" action="/admin/config/nav_add" method="post">
                                	<input name="id" value="<?php echo @$nav['id'];?>" type="hidden">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">上级：</label>
                                        <div class="col-sm-8">
                                        		<input name="parentid" value="<?php echo isset($nav['parentid']) ? $nav['parentid'] : 0;?>" type="hidden">
												<input id="name" class="form-control" name="top_name" required="" placeholder="<?php echo isset($nav['parentid']) && $nav['parentid']!=0?$nav['parent_name']:'顶级菜单';?>" aria-required="true" disabled="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">名称:</label>
                                        <div class="col-sm-8">
                                            <input id="name" class="form-control" name="name" value="<?php if(isset($_GET['id'])) echo @$nav['name'];?>" required="" aria-required="true">
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">地址:</label>
                                        <div class="col-sm-8">
                                            <input id="href" class="form-control" name="href" value="<?php echo @$nav['href'];?>" required="" aria-required="true">
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label class="col-sm-3 control-label">排序:</label>
                                        <div class="col-sm-8">
                                            <input id="sort" class="form-control" name="sort" value="<?php echo isset($nav['sort'])?$nav['sort']:0;?>" required="" aria-required="true">
                                        </div>
                                    </div>                                   
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">状态:</label>
                                        <div class="col-sm-8">
                                        	<label><input <?php if(empty($nav['stauts']) || (isset($nav['stauts']) && $nav['stauts']==1) ) echo 'checked=""';?> value="1" id="status" name="status" type="radio">显示</label>
                                        	<label><input <?php if(empty($nav['stauts']) && isset($nav['stauts'])) echo 'checked=""';?> value="0" id="status" name="status" type="radio">隐藏</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-3">
                                            <button class="btn btn-primary" type="submit">提交</button>
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


<script type="text/javascript">
$(function() {
	function totime(str){
			var str = str.replace(/-/g,"/");
			var date = new Date(str); 
			var humanDate = new Date(Date.UTC(date.getFullYear(),date.getMonth(),date.getDate(),date.getHours(),date.getMinutes(), date.getSeconds()));
			str = (humanDate.getTime()/1000 - 8*60*60);
			return str;
	}
		$('#btn_save').click(function(){
			var type = $('#type').val();
			var link_id = $('#link_id').val();
			var title = encodeURIComponent($('#title').val());
			var zhubo1 = $('#zhubo1').val();
			var zhubo2 = $('#zhubo2').val();
			var start_time = totime($('#start_time').val());
			var end_time = totime($('#end_time').val());
			var banner_url = encodeURIComponent($('#banner_url').val());
			var file_type = $('#file_type').val();
			var league_id = 'NULL';
			var action = 'http://<?php echo $_SERVER["HTTP_HOST"];?>/show/createshow/';
			var url = action+title+'/'+title+'/'+type+'/'+link_id+'/'+zhubo1+'/'+zhubo2+'/'+start_time+'/'+end_time+'/'+banner_url+'/'+file_type+'/'+league_id+'/api?';
			var sign = encodeURIComponent(url)+"<?php echo $api_key;?>";
			url = url+'sign='+$.md5(sign);
			$.get(url,function(data){
				if(data.state_code != ''){
					var data = JSON.parse(data);
					alert(data.state_code+"："+data.state_desc);
				}else{
					alert("-1：程序有错！");
				}
			})
		})
		
	$("#banner_temp_url").wrap("<form id='myupload' action='/admin/show/upload_file' method='post' enctype='multipart/form-data'></form>");
    $("#banner_temp_url").change(function(){
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			success: function(data) {
				var html = '<label class="col-sm-2 control-label">海报地址</label>';
                    html += '<div class="col-sm-10">';
                    html +='<input name="banner_url" id="banner_url" disabled="" class="form-control" value="'+data.file_url+'" />';
                    html +='<input name="file_type" id="file_type" disabled="" type="hidden" value="'+data.file_type+'" />';
                    html += '</div>';
                   $("#banner_url_ajax").html(html);
			}
		});
	});
		
})
</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/adddate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script>  