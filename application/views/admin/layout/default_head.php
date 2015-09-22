<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <link rel="shortcut icon" href="<?php echo $this->config->item('static');?>/img/ico.ico" type="image/x-icon" /> 
    <title>聊球-后台管理-<?php echo $child_title['name'];?></title>
    <meta name="keywords" content="聊球-后台管理,后台bootstrap框架,会员中心主题,后台HTML,响应式后台">
    <meta name="description" content="聊球-后台管理是一个完全响应式，基于Bootstrap3最新版本开发的扁平化主题，她采用了主流的左右两栏式布局，使用了Html5+CSS3等现代技术">

    <link href="<?php echo $this->config->item('static');?>/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="<?php echo $this->config->item('static');?>/font-awesome/css/font-awesome.css?v=4.3.0" rel="stylesheet">

    <!-- Morris -->
    <link href="<?php echo $this->config->item('static');?>/css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="<?php echo $this->config->item('static');?>/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link href="<?php echo $this->config->item('static');?>/css/animate.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('static');?>/css/style.css?v=2.2.0" rel="stylesheet">
	<script src="<?php echo $this->config->item('static');?>/js/jquery-2.1.1.min.js"></script>
</head>

<body>
    <div id="wrapper">
    	<!-- 导航start -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="<?php echo $this->config->item('static');?>/img/profile_small.png" style="width: 64px; height: 64px;"/>
                             </span>
                            <a class="dropdown-toggle" >
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo @$_COOKIE['username'];?></strong>
                             </span> <span class="text-muted text-xs block"><?php echo @$_COOKIE['role'];?> </span> </span>
                            </a>
                            
                        </div>
                        <div class="logo-element">
                            聊球
                        </div>
                    </li>
          <?php 
          foreach ($nav_list as $v) {?>
                    <li <?php if($v['parent']['id']==$sid) echo 'class="active"';?>>
                        <a href="<?php echo $v['parent']['href'];?>">
                        	<i class="fa fa-th-large"></i>
                        	<span class="nav-label"><?php echo $v['parent']['name'];?></span>
                        	 <span class="fa arrow"></span>
                        	 <?php if($v['parent']['name']=='日常事务'){?>
                        	 	<?php $data_count = $jubao+$feedback;if($data_count>0){?>
                        	 	<span class="label label-danger pull-right"><?php echo $data_count;?></span>
                        	 <?php }}?>
                        </a>
                        <ul class="nav nav-second-level">
                        	<?php 
                                if(!empty($v['child'])){
                                foreach ($v['child'] as $item) {
                                ?>
                            	<li>
                            		<a href="<?php echo $item['href'];?>?sid=<?php echo $item['parentid'];?>&cid=<?php echo $item['id'];?>" <?php if($item['id']==$cid) echo 'class="hover"';?>>
                            			<?php echo $item['name'];?>
                            			<?php if($item['name']=='举报信息' && $jubao>0){?>
                            				<span class="label label-danger pull-right"><?php echo $jubao;?></span>
                            			<?php }?>
                            			<?php if($item['name']=='用户反馈' && $feedback>0){?>
                            				<span class="label label-danger pull-right"><?php echo $feedback;?></span>
                            			<?php }?>
                            		</a>
                            	</li>
                            <?php 
                                }
                                }
                            ?>      
                        </ul>
                    </li>
         <?php }?>          
                </ul>

            </div>
        </nav>
		<!-- 侧边栏end -->
<!-- 顶部导航start -->
  <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary "><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message"><a href="/admin/index" title="返回首页"><i class="fa fa-home"></i></a>欢迎使用聊球后台</span>
                        </li>
                        <!--
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="index.html#">
                                <i class="fa fa-envelope"></i> <span class="label label-warning">16</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="index.html#">
                                <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                            </a>
                        </li>
                        -->
                        <li>
                            <a href="/admin/login/out">
                                <i class="fa fa-sign-out"></i> 退出
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>
            <!-- 顶部导航end -->
<?php if(!empty($parent_title) || !empty($child_title)){?>            
<!-- 顶部标题start -->            
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2><?php echo $child_title['name'];?></h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/admin/index">主页</a>
                        </li>
                        <?php if(!empty($parent_title)){?>
                        <li>
                            <a href="<?php echo $parent_title['href'];?>"><?php echo $parent_title['name'];?></a>
                        </li>
                        <?php }?>
                        <?php if(!empty($child_title)){?>
                        <li>
                            <strong> <a href="javascript:"><?php echo $child_title['name'];?></a></strong>
                        </li>
                        <?php }?>
                    </ol>
                </div>
            </div> 
<!-- 顶部标题end --> 
<?php }?>                      		