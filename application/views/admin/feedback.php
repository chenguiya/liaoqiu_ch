    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">                                        
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>内容</th>
                                            <th>联系方式</th>
                                            <th>用户</th>
                                            <th>版本</th>
                                            <th>系统</th>
                                            <th>牌子</th>
                                            <th>型号</th>
                                            <th>反馈时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="center"><?php echo $v['content'];?></td>
                                            <td class="center"><?php echo $v['contact_type'];?></td>
                                            <td class="center"><?php echo $v['member'];?></td>
                                            <td class="center"><?php echo $v['version'];?></td>
                                            <td><?php echo $v['type'];?></td>
                                            <td><?php echo $v['manufactor'];?></td>
                                            <td class="center"><?php echo $v['device'];?></td>
                                            <td class="center"><?php echo time_format($v['time']);?></td>
                                            <td>
                                            	<?php if($v['status']==0){?>
                                            	<a href="/admin/data/deal_with/<?php echo $v['id'];?>" class="btn btn-primary btn-xs">设为已处理</a>
                                            	<?php }else{?>
                                            	<a href="javascript:" class="btn btn-success btn-xs">已处理</a>	
                                            	<?php }?>
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
