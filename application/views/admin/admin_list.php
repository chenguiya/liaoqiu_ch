    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            
                            <div class="ibox-title">
                              <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">添加管理员</button>
                            </div>
                            <div class="ibox-content">
                                
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            
                                            <th>ID</th>
                                            <th>账号</th>
                                            <th>用户名</th>
                                            <th>角色</th>
                                            <th>登录时间</th>
                                            <th>登录IP</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($admin_list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="title"><?php echo $v['username'];?></td>
                                            <td class="center"><?php echo $v['name'];?></td>
                                            <td class="center"><?php echo $v['role_desc']; ?></td>
                                            <td class="center">
                                                <?php echo time_format($v['log_time']);?>
                                            </td>
                                            <td class="center">
                                                <?php echo long2ip($v['log_ip']);?>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm btn-click"   href="/admin/admin_role/get_one_manager?id=<?php echo $v['id']; ?>">修改</a>
                                                <button class="btn btn-primary btn-sm btn-click"  name="del_click" bid="<?php echo $v['id']; ?>" enabled="0">删除</button>
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

    

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">添加管理员</h4>
                </div>
                <div class="modal-body">
<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                	<div class="form-group">
                                        <label class="col-sm-2 control-label">登录名</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入登录名" type="text" name="username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入密码" type="text" name="password">
                                        </div>
                                    </div>       
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">确认密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入确认密码" type="text" name="re-password">
                                        </div>
                                    </div>     
                                  <div class="form-group">
                                        <label class="col-sm-2 control-label">真实姓名</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入真实姓名" type="text" name="name">
                                        </div>
                                    </div>
    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">所属角色</label>
                                        <div class="col-sm-10">
                                            <select name="role">
                                                <?php
                                                if(!empty($roles)){
                                                    foreach ($roles as $row)
                                                    {
                                                        
                                                ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                                <?php
                                                
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <button class="btn btn-primary" id="btn_save" type="button">保存</button>
                                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                        </div>
                                    </div>
                                </form>
                </div>
            </div>
        </div>
    </div>
    
    
    

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script type="text/javascript">

    
$(function() {
        
        $('#btn_save').click(function(){
                        var data = {
                            username :$('#myModal input[name="username"]').val(),
                            password : $('#myModal input[name="password"]').val(),
                            re_password : $('#myModal input[name="re-password"]').val(),
                            name :  $('#myModal input[name="name"]').val(),
                            role: $('#myModal select[name="role"]').val()
                        };
                        console.log(data);
                        if(data.username.length == 0)
                        {
                            alert('请输入登录名');
                            return false;
                        }
                        
                        if(data.password.length == 0 || data.re_password.length == 0 )
                        {
                            alert('请输入密码');
                            return false;
                        }
                        if( data.password != data.re_password)
                        {
                            alert('密码不一致');
                            return false;
                        }
                        if(data.name.length == 0)
                        {
                            alert('请输入真实的用户名');
                            return false;
                        }
                        var url =   '/admin/admin_role/manager_add/';
			$.post(url,data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('添加成功');
                                window.location.href =  '/admin/admin_role/manager/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
			}, 'json');
                        return false;
	});
        
        //删除
        $('button[name="del_click"]').click(function(){
            var data = {
                id : $(this).attr('bid')
            };
            var url = '/admin/admin_role/manager_del/'
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