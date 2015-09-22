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
                                            <th>类型</th>
                                            <th>比赛</th>
                                            <th>比分</th>
                                            <th>开始时间</th>
                                            <th>结束时间</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach ($match_list as $v) {?>										
                                        <tr class="gradeX">
                                        	<input type="hidden" name="league_id" class="league_id" value="<?php echo $v['league_id'];?>">
                                            <td class="match_id"><?php echo $v['id'];?></td>
                                            <td><?php echo $v['league_name'];?></td>
                                            <td class="title"><?php $title=$v['a_name']." VS ".$v['b_name'];echo $title;?></td>
                                            <td class="center"><?php echo $v['a_score']." : ".$v['b_score'];?></td>
                                            <td class="center" class="td_match_time">
                                            	<input type="hidden" name="league_id" class="match_time" value="<?php echo $v['match_time'];?>">
                                            	<?php echo time_format($v['match_time']);?></td>
                                            <td class="center" class="td_end_time"><input type="hidden" name="league_id" class="end_time" value="<?php echo $v['end_time'];?>">
                                            	<?php echo time_format($v['end_time']);?></td>
                                            <td><?php if($v['end_time']<time()){echo '<span class="label label-danger">已结束';}else if($v['match_time']>time() && $v['end_time']<time()){echo '<span class="label label-success">进行中';}else{echo '<span class="label label-primary">未开始';} ?></span></td>
                                            <td>
                                            	<?php if($v['display_liaogeqiu'] == 0){?>
                                            		<a href="/admin/show/show_add?type=1&<?php echo "title=".$title."&start_time=".time_format($v['match_time'])."&end_time=".time_format($v['end_time'])."&link_id=".$v['id']."&league_id=".$v['league_id'];?>" class="btn btn-primary">设为直播</a>
                                            	<?php }else{?>
                                            	<button class="btn btn-primary btn-sm btn_click  label-danger">已经直播</button>
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

