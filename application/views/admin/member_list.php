    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            
                            <div class="ibox-title">
                              <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">添加会员</button>
                            </div>
                            <div class="ibox-content">

                                <form name="form" role="form" class="form-inline" method="get" action="http://<?php echo $_SERVER["HTTP_HOST"];?>/admin/member/index/" style="margin-bottom:10px;">
                                    
                                    <div class="form-group">
                                        <label for="datepicker">注册时间：</label>
                                        <input type="text" value="<?php echo $params['start_time']; ?>" id="datepicker" readonly="readonly" name="start_time" class="form-control" /> -
                                        <input type="text" value="<?php echo $params['end_time'];?>" id="datepicker2" readonly="readonly" name="end_time" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        
                                        <div class="col-sm-10">
                                            <select name="role" class="form-control">
                                            <option value="" <?php if(empty($params['role'])){ echo 'selected';} ?> >所有用户</option>
                                            <option value="1"  <?php if($params['role'] == 1){ echo 'selected';} ?>  >普通用户</option>
                                            <option value="2"  <?php if($params['role'] == 2){ echo 'selected';} ?>  >主播</option>
                                            <option value="3" <?php if($params['role'] == 3){ echo 'selected';} ?>  >机器人</option>
                                            <option value="4" <?php if($params['role'] == 4){ echo 'selected';} ?>  >场控</option>
                                        </select>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <select name="status" class="form-control">
                                            <option value="" <?php if($params['status'] == ''){echo 'selected';}  ?> >状态</option>
					<option value="0"  <?php if($params['status'] === 0){echo 'selected';}  ?> >冻结</option>
					<option value="1"  <?php if($params['status'] == 1){echo 'selected';}  ?> >正常</option>
                                        </select>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <select name="type" class="form-control">
                                            <option value="2"  <?php if($params['type'] == 2){echo 'selected';}  ?> >用户ID</option>
                                            <option value="5"   <?php if($params['type'] == 5){echo 'selected';}  ?> >昵称</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                        <input type="text" name="keyword" value="<?php echo $params['keyword']; ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <button class="btn btn-white" type="submit">搜索</button>
                                </form>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" value="" id="check_box"  onclick="selectall('id[]');">
                                            </th>
                                            <th>ID</th>
                                            <th>账号</th>
                                            <th>聊球角色</th>
                                            <th>最后登录时间</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($member_list as $v) {?>										
                                        <tr class="gradeX">
                                        	
                                            <td class="match_id"><input type="checkbox" value="<?php echo $v['id'];?>" name="id[]"></td>
                                            <td><?php echo $v['member_id'];?></td>
                                            <td class="title"><?php echo $v['account'];?></td>
                                            <td class="center"><?php echo $v['role_msg'];?></td>
                                            <td class="center" class="td_match_time">
                                            	<?php echo time_format($v['token_time']);?>
                                            </td>
                                            <td class="center">
                                                <?php
                                                    echo $v['status_msg'];
                                                ?>
                                            </td>
                                            <td>
                                            	<?php if($v['role'] != 2){?>
                                                <button class="btn btn-primary btn-sm btn_click" name="role_anchors" bid="<?php echo $v['id']; ?>" enabled="2">设为主播</button>
                                                <?php }?>
                                                <?php if($v['role'] != 4){?>
                                                <button class="btn btn-primary btn-sm btn_click" name="role_anchors" bid="<?php echo $v['id']; ?>" enabled="4">设为场控</button>
                                            	<?php }?>
                                                
                                                <?php if($v['role'] == 2){?>
                                            	<button class="btn btn-primary btn-sm btn_click  label-danger"  name="role_anchors" bid="<?php echo $v['id']; ?>" enabled="1">取消主播</button>
                                            	<?php }elseif($v['role'] == 4){?>
                                            	<button class="btn btn-primary btn-sm btn_click  label-danger"  name="role_anchors" bid="<?php echo $v['id']; ?>" enabled="1">取消场控</button>
                                            	
                                                <?php }?>
                                                
                                                <?php
                                                if($v['status'] == 1){
                                                ?>
                                                <button class="btn btn-primary btn-sm btn-click"  name="state_click" bid="<?php echo $v['id']; ?>" enabled="0">冻结账号</button>
                                                <?php
                                                }else{
                                                ?>
                                                <button class="btn btn-primary btn-sm btn-click label-danger"   name="state_click" bid="<?php echo $v['id']; ?>" enabled="1">解冻账号</button>
                                                <?php
                                                }
                                                ?>
                                            	</td>
                                        </tr>
                   <?php }?>                     
                                        <tr class="gradeX">
                                            <td colspan="7">
                                                <label for="check_box" >全选/取消</label>
                                                <input type="submit" class="button" name="del_submit" value="删除" >
                                                <input type="submit" class="button" name="lock_submit" value="lock">
                                                <input type="submit" class="button" name="unlock_submit" value="unlock">
                                                <input type="button" class="button" name="move_submit"  value="move">
                                            </td>
                                        </tr>
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
                    <h4 class="modal-title" id="myModalLabel">添加聊球会员</h4>
                </div>
                <div class="modal-body">
<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                	<div class="form-group">
                                        <label class="col-sm-2 control-label">email</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入Email" type="text" name="email" id="email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入账号密码" name="passwd" id="passwd" type="password">
                                        </div>
                                    </div>                                  
                                  <div class="form-group">
                                        <label class="col-sm-2 control-label">昵称</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入账号昵称" type="text" name="account" id="account">
                                        </div>
                                    </div>
    
                                   <div class="form-group">
                                        <label class="col-sm-2 control-label">角色</label>
                                        <div class="col-sm-10">
                                            <select class="form-control m-b" name="role" id="role">
                                                <option value="1">普通会员</option>
                                                <option value="2">主播</option>
                                                <option value="3">机器人</option>
                                                <option value="4">场控</option>
                                            </select>
                                        </div>
                                    </div>
                                                                                                                                                                                          
                                    <div class="form-group">
                                        <div class="col-sm-4 col-sm-offset-2">
                                            <button class="btn btn-primary" id="btn_save" type="button">保存内容</button>
                                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                        </div>
                                    </div>
    
    
                                </form>
                </div>
            </div>
        </div>
    </div>
    
    
    

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script src="<?php echo $this->config->item('static');?>/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    
        //全选
        function selectall(name)
        {
            var el = document.getElementsByTagName('input');
            var len = el.length;
            for(var i = 0; i < len; i ++)
            {
                if((el[i].type == 'checkbox' ) && (el[i].name == name))
                {
                    el[i].checked = !el[i].checked;
                }
            }
            return true;
        }      
    
$(function() {
        
        var base_url = 'http://<?php echo $_SERVER["HTTP_HOST"];?>';
        
        $( "#datepicker, #datepicker2" ).datepicker({
            inline: true
        });
        
        function selectIds()
        {
            var ids = [];
            var el = document.getElementsByTagName('input');
            var len = el.length;
            var name="id[]";
            for(var i = 0; i < len; i ++)
            {
                if((el[i].type == 'checkbox') && (el[i].name == name) && (el[i].checked == true))
                {
                    ids.push(el[i].value);
                }
            }
            
            return ids;
        }
        $('input[name="unlock_submit"]').click(function(){
            var result = confirm('是否解冻账号！');
            
            if(!result)
            {
                return false;
            }
            var ids = selectIds();
            if(ids.length == 0)
            {
                alert('请选择需要解冻的记录');
                return false;
            }
            
            var data = {
                id : ids,
                status : 1,
            };
            var url = base_url + '/admin/member/changestate/';
            $.post(url, data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = base_url + '/admin/member/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
            }, 'json');
            
            return true;
        });
        $('input[name="lock_submit"]').click(function(){
            var result = confirm('是否冻结账号！');
            
            if(!result)
            {
                return false;
            }
            var ids = selectIds();
            if(ids.length == 0)
            {
                alert('请选择需要冻结记录');
                return false;
            }
            
            var data = {
                id : ids,
                status : 0,
            };
            var url = base_url + '/admin/member/changestate/';
            $.post(url, data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = base_url + '/admin/member/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
            }, 'json');
            
            return true;
        });
        
        //删除
        $('input[name="del_submit"]').click(function(){
            
        });
        
        $('#btn_save').click(function(){
                        var data = {
                            email :$('#email').val(),
                            passwd : $('#passwd').val(),
                            account : $('#account').val(),
                            role :  $('#role').val()
                        };
                        var url =  base_url + '/admin/member/add/';
			$.post(url,data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('添加成功');
                                window.location.href = base_url + '/admin/member/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
			}, 'json');
                        return false;
	});
        //设置主播
        $('button[name="role_anchors"]').click(function(){
            var data = {
                id : $(this).attr('bid'),
                role: $(this).attr('enabled')
            };
            var url = base_url + '/admin/member/setrole/';
            $.post(url, data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = base_url + '/admin/member/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
            }, 'json');
        });
        
        //冻结账号
        $('button[name="state_click"]').click(function(){
            var data = {
                id : $(this).attr('bid'),
                status: $(this).attr('enabled')
            };
            
            var url = base_url + '/admin/member/changestate/';
            $.post(url, data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = base_url + '/admin/member/index/';
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