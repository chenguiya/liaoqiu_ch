<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <link rel="shortcut icon" href="" type="image/x-icon" /> 
    <title>广场，聊球-一个人看球？不如一群人聊球</title>
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
 </head>

<body style="background-color: #fff">
	<div class="list-group" style="background-color: #D55039;padding:5px!important;color: #fff">
		<h3 class="text-center">聊球广场</h3>
	</div>
	<div class="feed-element" style="margin-top:3px;">
                                            <a href="profile.html#" class="pull-left" style="margin-left:3px;">
                                                <img alt="image" class="img-circle" src="<?php echo $row['member_logo'];?>">
                                            </a>
                                            <div class="media-body" style="margin-right:5px;">
                                                <small class="pull-right"><i class="fa fa-eye"></i> <?php echo $row['view_num'];?></small>
                                                <strong><?php echo $row['account'];?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo date('Y-m-d',$row['w_time']);?> 来自 广场</small>
                                                <p style="text-align:justify; text-justify:inter-ideograp">
                                                	<?php echo $row['content'];?>
												</p>
                                                <div class="ibox-content no-padding border-left-right">
                                                   <?php if(isset($row['file_path'])){
                                        					if($row['file_type']==0){
                                        			?>
                                        						<img  class="img-responsive" src="<?php echo $row['file_path'];?>">
                                        			<?php }else{?>
											                   	<video width="320" height="240" controls="controls" autoplay="autoplay">
																	<source src="<?php echo $row['file_path']?>" type="video/mp4" />
																</video>                                       				
                                        		<?php }}?>
                                                </div>
                                            </div>
                                        </div>   
<div class="panel-body">

                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab-1">
                                                        <div class="feed-activity-list">
                                                        	<h3>评论列表</h3>
<?php foreach ($comment_list as $v) {?>
                                                            <div class="feed-element">
                                                                <a href="" class="pull-left">
                                                                    <img class="img-circle" src="<?php echo $v['member_logo']?>">
                                                                </a>
                                                                <div class="media-body ">
                                                                    <small class="pull-right"><?php echo date('Y-m-d',$v['wc_time']);?></small>
                                                                    <strong><?php echo $v['account']?></strong>
                                                                    <br>
                                                                    <small class="text-muted"></small>
                                                                    <div class="well">
                                                                        <?php echo $v['content']?>
                                                                    </div>
                                                                </div>
                                                            </div>
 <?php }?>
 <?php if(count($comment_list)==0) {?>
  <div class="feed-element">
  	暂时还没有评论哦！
  </div>
<?php }?>
<div class="ibox-content no-padding border-left-right" style="background-color: #D55039;padding:5px!important;">
	<a href="http://www.5usport.com/download/app/liaoqiu/">
		<img class="img-responsive" src="http://www.5usport.com/ad/liaoqiu/logo.png">
	</a>
</div>                                                 
                                                    </div>
                                                </div>

                                            </div>
 </body>

</html>                                           