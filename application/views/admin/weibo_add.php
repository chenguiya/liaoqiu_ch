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
                                <h5><?php echo $title; ?></h5>
                            </div>
                            <div class="ibox-content">
                                                <form action="<?php if(!isset($row['w_id'])){ ?>/admin/weibo/add<?php }else { ?> /admin/weibo/edit <?php }?> "method="post" class="form-horizontal" enctype="multipart/form-data">
							<input name="w_id" id="w_id" type="hidden" value="<?php echo @$row['w_id']; ?>"   />
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label">内容</label>
		                            <div class="col-sm-10">
                                                <textarea rows="3" cols="80" id="content" name="content"><?php echo @$row['content']; ?></textarea>
		                            </div>
		                        </div>
		                        		                         
                                <div class="form-group">
                                        <label class="col-sm-2 control-label">图片或视频</label>
                                        <div class="col-sm-10">
                                            <input name="file" id="file" type="file" onchange="previewImage(this)" />
                                        </div>
                                    </div>		                                                                                        
		                        <div class="form-group">
		                            <label class="col-sm-2 control-label"></label>
		                            <div class="col-sm-10">
		                            	<div id="preview">
		                            		<?php if(isset($row['file_path'])){?><img style="margin-top: 0px;" src="<?php echo $row['file_path']?>" width="231" height="130" /><?php }?>
		                            	</div>
		                            </div>
		                        </div>
		                       	
                                                    
                                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">主播ID</label>
                                        <div class="col-sm-10">
                                        	<select class="form-control m-b" name="member_id" id="member_id">
                                                    <?php
                                                        if(!empty($roles))
                                                        {
                                                            foreach ($roles as $r)
                                                            {
                                                    ?>
                                                                                                <option value="<?php echo $r['member_id']; ?>" <?php if(isset($row['member_id']) && ($row['member_id'] == $r['member_id'])){
                                                                                                    echo 'selected';
                                                                                                } ?> ><?php echo $r['account']; ?></option>
                                                    <?php
                                                                
                                                            }
                                                        }
                                                    
                                                    ?>
                                                                                            </select>
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