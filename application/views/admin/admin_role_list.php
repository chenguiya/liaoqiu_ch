    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            
                            <div class="ibox-title">
                              <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">添加角色</button>
                            </div>
                            <div class="ibox-content">

                                <form name="form" role="form" class="form-inline" method="get" action="http://<?php echo $_SERVER["HTTP_HOST"];?>/admin/admin_role/index/" style="margin-bottom:10px;">
                                    
                                    
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <select name="status" class="form-control">
                                            <option value="" <?php if(trim($params['status']) == ''){echo 'selected';}  ?> >状态</option>
                                            <option value="0" <?php if(($params['status'] != '') && ($params['status'] == 0) ){echo 'selected';}  ?> >禁用</option>
                                            <option value="1" <?php if($params['status'] == 1){echo 'selected';}  ?> >正常</option>
                                        </select>
                                            </div>
                                    </div>
                                    
                                    <button class="btn btn-white" type="submit">搜索</button>
                                </form>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            
                                            <th>ID</th>
                                            <th>角色名称</th>
                                            <th>角色描述</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($admin_role_list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="title"><?php echo $v['name'];?></td>
                                            <td class="center"><?php echo $v['desc'];?></td>
                                            <td class="center"><?php echo $v['status_msg']; ?></td>
                                            <td>
                                                <a class="btn btn-primary btn-sm btn-click"   href="/admin/admin_role/get_one_distribute?id=<?php echo $v['id']; ?>" >权限设置</a>
                                                <button class="btn btn-primary btn-sm btn-click"  name="manager_click" bid="<?php echo $v['id']; ?>" enabled="0">成员管理</button>
                                                <a class="btn btn-primary btn-sm btn-click"   href="/admin/admin_role/get_one_role?id=<?php echo $v['id']; ?>">修改</a>
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

    
 
    
    
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">权限分配</h4>
                </div>
                <div class="modal-body">
<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                <?php
                                if(!empty($role_nav_list))
                                {
                                    
                                    foreach ($role_nav_list as $key => $nav)
                                    {
                                        
                                            ?>
                                        
    
                                        <div class="form-group draggable ui-draggable">
                                        <label class="col-sm-3 control-label"><?php echo $nav['parent']['name']; ?>：</label>
                                       
                                        <div class="col-sm-9">
                                             <?php
                                                if(!empty($nav['child']))
                                                {
                                                    foreach ($nav['child'] as $row)
                                                    {

                                            ?>
                                            <label class="checkbox-inline"><input pid="<?php echo $nav['parent']['id']; ?>" type="checkbox" manager="<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>" name="role_nav[]"><?php echo $row['name']; ?></label>
                                            
                                       <?php
                                       
                                                }
                                            }
                                       ?>
                                        </div>
                                        
                                    </div> 
                                        
                                <?php
                                
                                        
                                    }
                                }
                                ?>
                                    
                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <input type="hidden" name="id" value="" id="edit_distribute_id" />
                                            <button class="btn btn-primary" id="edit_btn_save" type="button" name="edit_distribute_click">保存</button>
                                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                        </div>
                                    </div>
                                </form>
                </div>
            </div>
        </div>
    </div>
    
    

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">添加角色</h4>
                </div>
                <div class="modal-body">
<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                	<div class="form-group">
                                        <label class="col-sm-2 control-label">角色名称</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入角色名称" type="text" name="name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">角色描述</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入角色描述" type="text" name="desc">
                                        </div>
                                    </div>                                  
                                  <div class="form-group">
                                        <label class="col-sm-2 control-label">是否启用</label>
                                        <div class="col-sm-10">
                                            <input type="radio" name="status" value="1" checked="true"> 正常
                                            <input type="radio" name="status" value="0"  > 禁用
                                        </div>
                                    </div>
    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">排序</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="排序" type="text" name="sort">
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

      $('button[name="distribute_click"]').click(function(){
           var data = {
                id : $(this).attr('bid')
            };
            $('#edit_distribute_id').val(data.id);
            $('input[manager]').attr("checked", false);
            var url =  '/admin/admin_role/get_role_nav/?' + new Date().getTime();
            
            
            
            $.get(url,data, function(result){console.log(2);
                if(result.state_code == 0)
                {
                    for(var key in result.data.manager)
                    {
                        var tmp_val = result.data.manager[key];
                        $('input[manager="' + tmp_val+ '"]').attr("checked", true) ;
                    }
                }
                else
                {
                    alert(result.state_desc);
                };
            }, 'json');
      });
      $('button[name="edit_distribute_click"]').click(function(){
          var data = {
                id : $('#edit_distribute_id').val(),
                manager:[]
            };
            $('input[name="role_nav[]"]:checked').each(function(){
                data.manager.push($(this).val());
                data.manager.push($(this).attr('pid'));
            });
            
           if(data.manager.length < 1)
           {
               alert('请分配权限');
               return false;
           }
            var url =  '/admin/admin_role/distribute/';
	    $.post(url,data, function(result){
                    if(result.state_code == 0)
                    {
                            alert('添加成功');
                            window.location.href = '/admin/admin_role/index/';
                    }
                    else
                    {
                            alert(result.state_desc);
                    }
            }, 'json');
                        return false;
      });
        
        $('#btn_save').click(function(){
                        var data = {
                            name :$('#myModal input[name="name"]').val(),
                            desc : $('#myModal input[name="desc"]').val(),
                            status : $('#myModal input[name="status"]:checked').val(),
                            sort :  $('#myModal input[name="sort"]').val()
                        };
                        
                        if(data.name.length == 0)
                        {
                            alert('请输入角色名称');
                            return false;
                        }
                        
                        if(data.desc.length == 0)
                        {
                            alert('请输入角色描述');
                            return false;
                        }
                        
                        var url =  '/admin/admin_role/add/';
			$.post(url,data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('添加成功');
                                window.location.href = '/admin/admin_role/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
			}, 'json');
                        return false;
	});

        
        //成员管理
        $('button[name="manager_click"]').click(function(){
            var data = {
                id : $(this).attr('bid')
            };
            window.location.href =  '/admin/admin_role/manager/';
            
        });
        
        //删除
        $('button[name="del_click"]').click(function(){
            var data = {
                id : $(this).attr('bid')
            };
            var url =  '/admin/admin_role/del/'
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