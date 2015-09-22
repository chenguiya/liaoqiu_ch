           <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>登录信息</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <p>真实姓名：<?php echo $member['name']; ?></p>
                                        <p>所属角色：<?php echo $role['name']; ?></p>
                                        <p>最近登录时间：<?php echo time_format($member['log_time']); ?></p>
                                        <p>最近登录IP：<?php echo long2ip($member['log_ip']); ?></p>
                                        <p><button   data-toggle="modal" data-target="#myModal">修改密码或姓名</button></p>
                                        
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>注册用户信息</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <p>总共注册用户人数：<?php echo $count; ?>  个</p>
                                        
                                        <p>总共注册主播人数：<?php echo $anchor_count; ?>  个</p>
                                        
                                        <p>总共注册未禁用人数：<?php echo $use_count; ?>  个</p>
                                        <p>总共注册禁用人数：<?php echo $not_use_count; ?>  个</p>
                                        <p>总共一周内注册人数：<?php echo $day_count; ?>  个</p>
                                        <p>总共一月内注册人数：<?php echo $month_count; ?> 个</p>
                                        
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>更新日志</h5>
                                    </div>
                                    <div class="ibox-content no-padding">
                                        
                                        <div class="panel-body">
                                            <div class="panel-group" id="version">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h5 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#version" href="index.html#v22">v2.2</a><code class="pull-right">2015.08.27更新</code>
                                            </h5>
                                                    </div>
                                                    <div id="v22" class="panel-collapse collapse in">
                                                        <div class="panel-body">
                                                            <ol>
                                                                <li>紧张开发中</li>
                                                                <!--
                                                                <li>从现在起，Hplus有开发文档啦，解压后在docs目录下；
                                                                </li>
                                                                <li>根据用户的反馈，根据用户的反馈，移除了CDN支持，CDN服务将于2015年6月30日之后结束支持，如果您正在使用CDN服务，请尽快完成迁移，对于给您造成的不便，我们表示非常抱歉；
                                                                </li>
                                                                <li>升级Bootstrap到最新版本v3.4.0；
                                                                </li>
                                                                <li>修改了style.css，修复了其中的一些bug；；
                                                                </li>
                                                                -->
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
                
                
                

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">修改会员资料</h4>
                </div>
                <div class="modal-body">
<form method="post" class="form-horizontal" action="" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">真实姓名</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入真实姓名" type="text" name="name" id="name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">密码</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" placeholder="输入账号密码" name="password" id="password" type="password">
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
<script type="text/javascript">
    
$(function() {
                
        //冻结账号
        $('#btn_save').click(function(){
            var data = {
                name : $('#name').val(),
                password: $('#password').val()
            };
            if((data.name.length == 0 ) && (data.password.length == 0))
            {
                alert('请输入真实姓名或密码');
                return false;
            }
            
            $.post('/admin/index/update_admin/', data, function(result){
                            if(result.state_code == 0)
                            {
                                alert('设置成功');
                                window.location.href = 'admin/index/index/';
                            }
                            else
                            {
                                alert(result.state_desc);
                            }
            }, 'json');
        })
});

        
        
</script>
<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/jquery.form.js" ></script>