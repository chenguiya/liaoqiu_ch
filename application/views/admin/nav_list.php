    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                        	
                        	<div class="ibox-title">
                              <a href="/admin/config/nav_add?<?php echo $_SERVER['QUERY_STRING'];?>" class="btn btn-primary">新增菜单</a>
                            </div>
                            
                            <div class="ibox-content">                                          
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>菜单名称</th>
                                            <th>主导航状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($this_nav_list as $item) { foreach ($item as $k=>$v) {?>
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td width="60%"><?php $str=$k==0?'':' └─ ';echo $str.$v['name'];?></td>
                                            <td><?php if($v['status']==0){echo '<span class="label">隐藏';}else{echo '<span class="label label-success">显示';}?></span></td>
                                            <td>
                                            	<a href="/admin/config/nav_add?parentid=<?php echo $v['id'];?>" class="btn btn-primary " type="button"><i class="fa fa-check"></i>&nbsp;添加子菜单</a>&nbsp;
                                            	<a href="/admin/config/nav_add?id=<?php echo $v['id'];?>" class="btn btn-info " type="button"><i class="fa fa-paste"></i> 修改</a>&nbsp;
                                            	<a href="/admin/config/del_nav?id=<?php echo $v['id'];?>" class="btn btn-warning btn-danger" type="button"><i class="fa fa-warning"></i>  <span class="bold">删除</span></a>
											</td>
                                        </tr>
                   <?php }}?>                     
                                    </tbody>
                                </table>
							</div>
                        </div>
                    </div>
                </div>