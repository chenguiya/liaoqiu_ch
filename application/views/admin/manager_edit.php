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
                                <h5><?php echo $title; ?></h5>
                            </div>
                            <div class="ibox-content">
				<form method="post" class="form-horizontal" enctype="multipart/form-data">
				<div class="form-group">
                                        <label class="col-sm-2 control-label">登录名</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入登录名" value="<?php echo $manager['username']; ?>" type="text" name="username" id="edit_username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入密码" type="text" name="password" id="edit_password">
                                        </div>
                                    </div>       
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">确认密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入确认密码" type="text" name="re-password" id="edit_re_password">
                                        </div>
                                    </div>     
                                  <div class="form-group">
                                        <label class="col-sm-2 control-label">真实姓名</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入真实姓名"  value="<?php echo $manager['name']; ?>" type="text" name="name" id="edit_name">
                                        </div>
                                    </div>
    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">所属角色</label>
                                        <div class="col-sm-10">
                                            <select name="role" id="edit_role">
                                                <?php
                                                if(!empty($roles)){
                                                    foreach ($roles as $row)
                                                    {
                                                        
                                                ?>
                                                <option value="<?php echo $row['id']; ?>" <?php if($row['id'] == $manager['role']){ ?>selected="selected" <?php } ?> ><?php echo $row['name']; ?></option>
                                                <?php
                                                
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <input type="hidden" id="edit_id" value="<?php echo $manager['id']; ?>" />
                                            <button class="btn btn-primary" id="edit_btn_save" type="button">保存</button>
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
        $('#edit_btn_save').click(function(){
            var data = {
                id : $("#edit_id").val(),
                username :$('#edit_username').val(),
                password : $('#edit_password').val(),
                re_password : $('#edit_re_password').val(),
                name :  $('#edit_name').val(),
                role: $('#edit_role').val()
            };
            var url = '/admin/admin_role/manager_edit/'
            $.post(url, data, function(result){
                if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href =  '/admin/admin_role/manager/';
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