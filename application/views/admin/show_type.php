    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                	
                        <div class="ibox float-e-margins">
                        	<div class="ibox-title">
                        		<a href="?" class="btn btn-xs <?php echo !isset($_GET['type'])?'btn-primary':'btn-default';?>">话题类型列表</a>  
                        		<a href="?type=1" class="btn btn-xs  <?php echo isset($_GET['type'])?'btn-primary':'btn-default';?>">赛程类型列表</a>
                        		<a href="/admin/show/type_add" class="btn btn-xs btn-success">新建话题类型</a>
                        		<a href="/admin/show/type_add?type=1" class="btn btn-xs btn-success">新建赛程类型</a>
                            </div>
                            <div class="ibox-content">                                        
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>类别</th>
                                            <th>图标地址</th>
                                            <th>状态</th>
                                            <th>排序</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="center"><?php echo $v['title'];?></td>
                                            <td class="center"><img src="<?php echo $v['logo'];?>" width="36" /> <?php echo $v['logo'];?></td>
                                            <td>
                                            	<?php echo ($v['status']==0)?"禁用":"显示";?>
                                            </td>
                                            <td><?php echo $v['sort'];?></td>
                                            <td>
                                            	<?php if($v['status']==0){?>
                                            	<a href="/admin/show/type_eidt/<?php echo $v['id'];?>/1<?php if(isset($_GET['type'])) echo "?type=1";?>" class="btn btn-primary btn-xs">显示</a>
                                            	<?php }else{?>
                                            	<a href="/admin/show/type_eidt/<?php echo $v['id'];?>/0<?php if(isset($_GET['type'])) echo "?type=1";?>" class="btn btn-success btn-xs btn-danger">禁用</a>	
                                            	<?php }?>
                                            	<a href="/admin/show/type_add?id=<?php echo $v['id'];?><?php if(isset($_GET['type'])) echo "&type=1";?>" class="btn btn-xs btn-primary">编辑</a>	
                                            	<a href="/admin/show/type_del/<?php echo $v['id'];?><?php if(isset($_GET['type'])) echo "&type=1";?>" class="btn btn-success btn-xs btn-danger">删除</a>	
                                            </td>
                                        </tr>
                   <?php }?>                     
                                    </tbody>
                                </table>
							</div>
                        </div>
                    </div>
                </div>
