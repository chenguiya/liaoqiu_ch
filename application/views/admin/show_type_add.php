    <!-- Data Tables -->   
    <link href="<?php echo $this->config->item('static');?>/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">                                          
<!--表单s -->
					<div class="col-lg-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php echo @$_GET['title']?></h5>
                            </div>
                            <div class="ibox-content">
						<form action="/admin/show/type_add" method="post" class="form-horizontal" enctype="multipart/form-data">
							<input name="id" id="id" value="<?php echo @$row['id'];?>" type="hidden">
							<input name="type" id="type" value="<?php echo @$_GET['type'];?>" type="hidden">
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label">类别</label>
		                            <div class="col-sm-10">
		                            	<input type="text" class="form-control" id="title" name="title" value="<?php echo @$row['title'];?>" size="10" />
		                            </div>
		                        </div>
		                        <?php if(isset($row['league_id']) || isset($_GET['type'])){?> 
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label">联赛id</label>
		                            <div class="col-sm-10">
		                            	<input type="text" class="form-control" id="league_id" name="league_id" value="<?php echo @$row['league_id'];?>" size="10" />
		                            </div>
		                        </div>
		                         <?php }?>   		                         
                                <div class="form-group">
                                        <label class="col-sm-2 control-label">图标</label>
                                        <div class="col-sm-10">
                                            <input name="file" id="file" type="file" onchange="previewImage(this)" />
                                        </div>
                                    </div>		                                                                                        
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label"></label>
		                            <div class="col-sm-10">
		                            	<div id="preview">
		                            		<?php if(isset($row['logo'])){?><img style="margin-top: 0px;" src="<?php echo $row['logo']?>" width="231" height="130" /><?php }?>
		                            	</div>
		                            </div>
		                        </div>
		                        <div class="form-group" id="banner_url_ajax">
		                        	<label class="col-sm-2 control-label">图标地址</label>
					                   <div class="col-sm-10">
					                  <input name="logo" id="logo"  class="form-control" value="<?php echo @$row['logo']?>" />
					                  <span class="help-block m-b-none">如果选择了文件，则该地址无效</span>
					                  </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label">排序</label>
		                            <div class="col-sm-10">
		                            	<input type="text" class="form-control" id="sort" name="sort" value="<?php echo isset($row['sort'])?$row['sort']:0;?>" size="10" />
		                            </div>
		                        </div> 		                                                                                          
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label">状态:</label>
		                            <div class="col-sm-6">
		                            	<label><input value="1" name="status" type="radio" <?php if(empty($row['stauts']) || (isset($row['stauts']) && $row['stauts']==1) ) echo 'checked';?>>显示</label>
		                            	<label><input value="0" name="status" type="radio" <?php if(empty($row['stauts']) && isset($row['stauts'])) echo 'checked';?>>隐藏</label>
		                            </div>
		                        </div>				                                                                                                                                                                   
		                        <div class="form-group">
		                            <div class="col-sm-4 col-sm-offset-2">
		                                <button class="btn btn-primary" id="dosubmit" type="submit">提交</button>
		                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
		                            </div>
		                        </div>
		                    </form>
                            </div>
                        </div>
                    </div>
<!--表单e -->
							</div>
                        </div>
                    </div>
                </div>

<script type="text/javascript" src="<?php echo $this->config->item('static');?>/js/img_view.js" ></script> 