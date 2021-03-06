<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">

    <title>聊球-后台管理</title>
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

<body class="gray-bg">

    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name"><img src="<?php echo $this->config->item('static');?>/img/logo.png"</h1>

            </div>
            <h3>欢迎进入聊球应用后台</h3>

            <form class="m-t" role="form" action="" method="post">
                <div class="form-group">
                    <input type="input" name="username" class="form-control" placeholder="用户名" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="密码" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b" id="button_login">登 录</button>
                <!--
                <p class="text-muted text-center"> <a href="login.html#"><small>忘记密码了？</small></a> | <a href="register.html">注册一个新账号</a>
                </p>-->

            </form>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="<?php echo $this->config->item('static');?>/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo $this->config->item('static');?>/js/bootstrap.min.js?v=3.4.0"></script>

</body>

</html>
