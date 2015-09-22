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
                                <h5><?php echo $title;?></h5>
                            </div>
                            <div class="ibox-content">
				<form method="post" class="form-horizontal" enctype="multipart/form-data">
				
                                	<div class="form-group">
                                        <label class="col-sm-2 control-label">角色名称</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入角色名称" type="text" id="edit_name" name="name" value="<?php echo $role['name']; ?>" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">角色描述</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入角色描述" type="text" id="edit_desc" name="desc" value="<?php echo $role['desc']; ?>" >
                                        </div>
                                    </div>                                  
                                  <div class="form-group">
                                        <label class="col-sm-2 control-label">是否启用</label>
                                        <div class="col-sm-10">
                                            <input type="radio" name="status" id="edit_status_on" value="1" <?php if(!empty($role['status'])){ echo 'checked="true"'; } ?> > 正常
                                            <input type="radio" name="status" id="edit_status_off" value="0" <?php if(empty($role['status'])){ echo 'checked="true"';  } ?>  > 禁用
                                        </div>
                                    </div>
    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">排序</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="排序" id="edit_sort" type="text" name="sort"  value="<?php echo $role['sort']; ?>">
                                        </div>
                                    </div>
                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <input type="hidden" name="id" value="<?php echo $role['id']; ?>" id="edit_id" />
                                            <button class="btn btn-primary" id="edit_btn_save" type="button" name="edit_click">保存</button>
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

    
$(function() {
        
        
        //修改
        $('button[name="edit_click"]').click(function(){
            var data = {
                id : $("#edit_id").val(),
                name: $('#edit_name').val(),
                desc: $('#edit_desc').val(),
                sort: $('#edit_sort').val(),
                status:$('input[name="status"]:checked').val()
            };
            var url =  '/admin/admin_role/edit/'
            $.post(url, data, function(result){
                if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = '/admin/admin_role/index/';
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