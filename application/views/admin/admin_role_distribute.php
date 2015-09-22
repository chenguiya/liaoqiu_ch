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
                                            <label class="checkbox-inline">
                                                <input pid="<?php echo $nav['parent']['id']; ?>" <?php if(in_array($row['id'], $distribute['manager'])){echo 'checked="checked"';} ?>  type="checkbox" manager="<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>" name="role_nav[]">
                                                    <?php echo $row['name']; ?>
                                            </label>
                                            
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
                                            <input type="hidden" name="id" value="<?php echo $distribute['id']; ?>" id="edit_distribute_id" />
                                            <button class="btn btn-primary" id="edit_btn_save" type="button" name="edit_distribute_click">保存</button>
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
        
})

        
        
</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/adddate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script>  