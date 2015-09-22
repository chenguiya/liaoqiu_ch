    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
   <style>#preview img{width: 300px;}</style>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">                                          
<!--表单s -->
					<div class="col-lg-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>节目表单</h5>
                            </div>
                            <div class="ibox-content">
                                <form novalidate="novalidate" class="form-horizontal m-t" id="commentForm" action="/admin/show/show_add" method="post">
                                	<input name="id" id="id" value="<?php echo @$show['id'];?>" type="hidden">
                                	<input type="hidden" name="type" id="type" value="<?php echo isset($_GET['type'])?$_GET['type']:@$show['type'];?>">
                                	<input type="hidden" name="league_id" id="league_id" value="<?php echo isset($_GET['league_id'])?$_GET['league_id']:@$show['league_id'];?>">
                                	<?php if(@$_GET['type']==1 || @$show['type']==1){?>
                                	<input type="hidden" name="type" id="link_id" value="<?php echo isset($_GET['link_id'])?$_GET['link_id']:@$show['link_id'];?>">
									<?php }?>
									<?php if(@$_GET['type']==2 || @$show['type']==2){?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">话题类别</label>
                                        <div class="col-sm-10">
                                        	<select class="form-control m-b" name="link_id" id="link_id">
                                            	<?php foreach ($topic_type as $v) {?>
                                                <option value="<?php echo $v['id'];?>"><?php echo $v['title'];?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">内容</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" value="<?php echo isset($_GET['title'])?$_GET['title']:@$show['title'];?>" type="text" name="title" id="title">
                                        </div>
                                    </div>                                    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">第一主播</label>
                                        <div class="col-sm-10">
                                            <select class="form-control m-b" name="zhubo1" id="zhubo1">
                                            	<?php foreach ($zhubo_list as $v) {?>
                                                <option value="<?php echo $v['member_id']?>"><?php echo $v['nick_name']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">第二主播</label>
                                        <div class="col-sm-10">
                                            <select class="form-control m-b" name="zhubo2" id="zhubo2">
                                            	<option value="0">请选择</option>
                                            	<?php foreach ($zhubo_list as $v) {?>
                                                <option value="<?php echo $v['member_id']?>"><?php echo $v['nick_name']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">第一场控</label>
                                        <div class="col-sm-10">
                                            <select class="form-control m-b" name="changkong1" id="changkong1">
                                                <option value="0">请选择</option>
                                            	<?php foreach ($changkong_list as $v) {?>
                                                <option value="<?php echo $v['member_id']?>"><?php echo $v['nick_name']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">第二场控</label>
                                        <div class="col-sm-10">
                                            <select class="form-control m-b" name="changkong2" id="changkong2">
                                                <option value="0">请选择</option>
                                            	<?php foreach ($changkong_list as $v) {?>
                                                <option value="<?php echo $v['member_id']?>"><?php echo $v['nick_name']?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">开始时间</label>
                                        <div class="col-sm-10">
                                        <?php 
                                        	if(@$_GET['type']==2 || @$show['type']==2){  //话题
                                        		$start_time=isset($show['start_time'])?$show['start_time']:time();
												$start_time=date('Y-m-d H:i:s',$start_time);		
												$end_time=isset($show['end_time'])?$show['end_time']:time()+3600;
												$end_time=date('Y-m-d H:i:s',$end_time);
												$disabled='';
											}else{ //赛程
												$disabled='disabled=""';
												$start_time=isset($_GET['start_time'])?$_GET['start_time']:date('Y-m-d H:i:s',$show['start_time']);
												$end_time=isset($_GET['end_time'])?$_GET['end_time']:date('Y-m-d H:i:s',$show['end_time']);
											}
										 ?>
                                        	<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" class="form-control" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')" <?php echo $disabled;?>/>
                                        </div>
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">结束时间</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>"  class="form-control" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')" <?php echo $disabled;?>/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">海报图</label>
                                        <div class="col-sm-10">
                                            <input name="file" id="file" type="file" onchange="previewImage(this)" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"></label>
                                        <div class="col-sm-10">
                                        	<div id="preview">
                                        		<?php if(isset($show['banner_url'])){
                                        					if($show['file_type']==0){
                                        			?>
                                        						<img style="margin-top: 0px;" src="<?php echo $show['banner_url']?>" width="231" height="130" />
                                        			<?php }else{?>
											                   	<video width="320" height="240" controls="controls" autoplay="autoplay">
																	<source src="<?php echo $show['banner_url']?>" type="video/mp4" />
																</video>                                       				
                                        		<?php }}?>
                                        	</div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="banner_url_ajax">
                                    	<?php if(isset($show['banner_url'])){?>
                                    	<label class="col-sm-2 control-label">海报地址</label>
						                   <div class="col-sm-10">
						                  <input name="banner_url" id="banner_url" disabled="" class="form-control" value="<?php echo $show['banner_url']?>" />
						                 <input name="file_type" id="file_type" disabled="" type="hidden"  value="<?php echo $show['file_type']?>" />
						                  </div>
						                  <?php }?>
                                    </div>
<?php if(@$_GET['league_id']==1 || @$show['league_id']==1){?>                                    
                                    <div class="form-group" id="banner_url_ajax">
                                    	<label class="col-sm-2 control-label">英超链接</label>
						                   <div class="col-sm-10">
					<input class="form-control" value="<?php echo @$show['premier_url'];?>" type="text" name="premier_url" id="premier_url">	                   	
						                  </div>
                                    </div>
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">直播开始时间</label>
                                        <div class="col-sm-10">
                                        	<input type="text" name="premier_start_time" id="premier_start_time" value="<?php $time=isset($show['premier_start_time'])?$show['premier_start_time']:time(); echo date('Y-m-d H:i:s',$time);?>" class="form-control" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')"/>
                                        </div>
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">直播结束时间</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="premier_end_time" id="premier_end_time"  value="<?php $time=isset($show['premier_end_time'])?$show['premier_end_time']:time()+3600; echo date('Y-m-d H:i:s',$time);?>" class="form-control" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')"/>
                                        </div>
                                    </div>
<?php }?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">状态:</label>
                                        <div class="col-sm-6">
                                        	<label><input value="1" name="status" type="radio" <?php if(empty($nav['stauts']) || (isset($nav['stauts']) && $nav['stauts']==1) ) echo 'checked';?>>显示</label>
                                        	<label><input value="0" name="status" type="radio" <?php if(empty($nav['stauts']) && isset($nav['stauts'])) echo 'checked';?>>隐藏</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-3">
                                             <button class="btn btn-primary" id="btn_save" type="button">保存内容</button>
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
                    var data = {
                        id :$('#id').val(),
                        type:$('#type').val(),
                        link_id:$('#link_id').val(),
                        title:encodeURIComponent($('#title').val()),
                        zhubo1:$('#zhubo1').val(),
                        zhubo2:$('#zhubo2').val(),
                        changkong1:$('#changkong1').val(),
                        changkong2:$('#changkong2').val(),
                        start_time:totime($('#start_time').val()),
                        end_time:totime($('#end_time').val()),
                        banner_url:encodeURIComponent($('#banner_url').val()),
                        file_type:$('#file_type').val(),
                        league_id: $('#league_id').val(),
                        status:$('input:radio[name="status"]:checked').val(),
                        premier_url:'0',
                        premier_start_time:'0',
                        premier_end_time:'0'
                    };
       <?php if(@$_GET['league_id']==1 || @$show['league_id']==1){?> 
                data.premier_url = $('#premier_url').val();
                data.premier_start_time = totime($('#premier_start_time').val());
                data.premier_end_time = totime($('#premier_end_time').val());
       <?php } ?>
       if(!data.title){
       	alert('标题不能为空！');return;
       }
       if(!data.banner_url){
       	alert('请上传封面！');	return;
       }
			var url = '/admin/show/show_add/';
			$.post(url,data,function(data){
				if(data.state_code != ''){
					var data = JSON.parse(data);
					alert(data.state_code+"："+data.state_desc);
				}else{
					alert("-1：程序有错！");
				}
			})
		})
	$("#file").wrap("<form id='myupload' action='/admin/show/upload_file' method='post' enctype='multipart/form-data'></form>");
    $("#file").change(function(){
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			success: function(data) {
				var html = '<label class="col-sm-2 control-label">海报地址</label>';
                    html += '<div class="col-sm-10">';
                    html +='<input name="banner_url" id="banner_url" disabled="" class="form-control" value="'+data.file_url+'" />';
                    html +='<input name="file_type" id="file_type" disabled="" type="hidden" value="'+data.file_type+'" />';
                    html += '</div>';
                   $("#banner_url_ajax").html(html);
                   if(data.file_type==1){
                   	var v = '<video width="320" height="240" controls="controls" autoplay="autoplay">';
						v += '<source src="'+data.file_url+'" type="video/mp4" />';
						v += '</video>';
                   }
                   $("#preview").html(v);
			}
		});
	});

<?php if(isset($_GET['id'])){?>
//选择选择表单的当前状态
function select(key){
	switch(key)
	{
	case 'zhubo1':
		var val = "<?php echo $show['zhubo1']?>";
	  break;
	case 'zhubo2':
		var val = "<?php echo $show['zhubo2']?>";
	  break;
	case 'link_id':
		var val = <?php echo $show['link_id']?>;
	  break;	  	  
        
        case 'changkong1':
            var val = "<?php echo $show['changkong1']?>";
        break;
        case 'changkong2':
            var val = "<?php echo $show['changkong2']?>";
        break;
	default:
		alert('你的参数有误！');
		break;	 
	}
        
		if(!val || val.length == 0) return;
		$('#'+key).val(val);
		$('#'+key+' option').each(function(){ 
			if( $(this).val() == val){ 
			var name = $(this).html();
				var html = '<option value="'+val+'">'+name+'</option>';
				$('#'+key).prepend(html);
			} 
		}); 
}	

select('zhubo1');
select('zhubo2');
select('link_id');
select('changkong1');
select('changkong2');
<?php }?>
})

</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/adddate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script>  