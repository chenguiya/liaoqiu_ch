    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
<!-- 搜索 s--> 
                    <div class="input-group col-sm-6">
                    	<form action="/admin/data/chat_report" method="get">
                             <input class="form-control" name="show_id" value="<?php echo @$_GET['show_id']?>" placeholder="请输入节目ID" type="text">
							<span class="input-group-btn"> <button type="submit" class="btn btn-primary">搜索</button> </span>
                        </form>
                    </div>
<!-- 搜索 e-->                                           
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>举报人</th>
                                            <th>被举报人</th>
                                            <th>节目ID</th>
                                            <th>举报理由</th>
                                            <th>环信聊天记录ID</th>
                                            <th>举报时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($report_list as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="center"><?php echo $v['member_id'];?></td>
                                            <td class="center"><?php echo $v['beijubao_member_id'];?></td>
                                            <td><?php echo $v['show_id'];?></td>
                                            <td class="center"><?php echo $v['content'];?></td>
                                            <td><?php echo time_format($v['jubao_time']);?></td>
                                            <td class="center"><?php echo $v['chatlogs_uuid'];?></td>
                                            <td>
                                            	<?php if($v['operate_status']==0){?>
                                            	<a href="/admin/data/see_chat_report/<?php echo $v['id'];?>" class="btn btn-primary btn-xs">设为已处理</a>
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
