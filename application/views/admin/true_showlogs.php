    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                        	
                        	<div class="ibox-title">
                        		<h3><?php echo @$_GET['title']?></h3>
                        		总人数：<?php echo $all_count?>人  真实人数：<?php echo $true_count?>人
                            </div>
                            
                            <div class="ibox-content">
<!-- 搜索 s--> 
                    <div class="input-group">
                             <input class="form-control" placeholder="请输入用户昵称" type="text"> <span class="input-group-btn"> <button type="button" class="btn btn-primary">搜索
                                        </button> </span>
                                            </div>
<!-- 搜索 e-->                                             
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>用户ID</th>
                                            <th>昵称</th>
                                            <th>注册时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($member_arr as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['member_id'];?></td>
                                            <td class="center"><?php echo $v['account'];?></td>
                                            <td class="center"><?php echo time_format($v['token_time']);?></td>
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
