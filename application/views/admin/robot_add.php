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
				                                        <label class="col-sm-2 control-label">添加人数</label>
				                                        <div class="col-sm-10">
				                                        	<input type="text" id="num" name="num" size="10" class="form-control" >
				                                        </div>
				                                    </div> 
				                                    <div class="form-group">
				                                        <label class="col-sm-2 control-label">节目ID</label>
				                                        <div class="col-sm-10">
				                                        	<input type="text" class="form-control" id="show_id" name="show_id" value="<?php echo $id;?>"  disabled="" size="10" />
				                                        </div>
				                                    </div>                                                                 
				                                    <div class="form-group">
				                                        <div class="col-sm-4 col-sm-offset-2">
				                                            <button class="btn btn-primary" id="dosubmit" type="button">确定</button>
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

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script type="text/javascript">
$(function(){     
	$('#dosubmit').click(function(){
		var num = $('#num').val();
		var id = $('#show_id').val();
		var action = "<?php echo $this->config->item('domain')?>/robot/entershow?";
		var key = '<?php echo $this->config->item('api_key')?>';
		var sign = $.md5(encodeURIComponent(action)+key);
		url = action+'sign='+sign;
		if(num){
			$.get(url,{num:num,id:id},function(data){
				if(data){
					alert(data);
				}
			})
		}else{
			alert('人数不能为空！');
		}
		
    });
    
    function getRandom(n){
        return Math.floor(Math.random()*n+1)
    }
});
</script>