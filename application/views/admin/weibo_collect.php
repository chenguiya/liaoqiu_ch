    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style>#preview img{width: 300px;}</style>
            
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            
                            <div class="ibox-title">
                              <a href="/admin/weibo/add" class="btn btn-primary">发布微博</a>
                            </div>
                            <div class="ibox-content">
                                
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            
                                            <th>ID</th>
                                            <th>账号</th>
                                            <th>微博ID</th>
                                            <th>微博内容</th>
                                            <th>状态</th>
                                            <th>时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($collect as $v) {?>										
                                        <tr class="gradeX">
                                            <td><?php echo $v['id'];?></td>
                                            <td class="title"><?php echo $v['account'];?></td>
                                            <td class="center"><?php echo $v['weibo_id'];?></td>
                                            <td class="center"><?php echo $v['content']; ?></td>
                                            <td class="center"><?php 
                                                if($v['status'] == 1)
                                                {
                                                    echo '收藏';
                                                }
                                                else
                                                {
                                                    echo '取消收藏';
                                                }
                                            ?></td>
                                            <td class="center">
                                                <?php echo time_format($v['time']);?>
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

     
    

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.md5.js" ></script>
<script type="text/javascript">

    
$(function() {
        
        //删除
        $('a[name="del_click"]').click(function(){
            var data = {
                wid : $(this).attr('bid')
            };
            var url = '/admin/weibo/delweibo/'
            $.post(url, data, function(result){
                if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href =  '/admin/weibo/getweibolist/';
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