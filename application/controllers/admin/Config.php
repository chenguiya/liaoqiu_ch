<?php 

include_once("Base.php");
class Config extends Base {

	//导航列表	
	public function nav_list()
	{
		$this->load->model('liaoqiu_nav_model');
		$list = $this->liaoqiu_nav_model->getRecord();
		$nav_list = array();
		foreach ($list as $k => $v) {
			if($v['parentid']) $nav_list[$v['parentid']][$k+1] = $v;
				else $nav_list[$v['id']][0] = $v;
		}
		$this->data['this_nav_list'] =  $nav_list;
		$this->show('nav_list',$this->data);
	}

	//新增/修改导航	
	public function nav_add()
	{
		if($_POST){
			if(!is_numeric($_POST['parentid']) || empty($_POST['name']) || empty($_POST['href']) || !is_numeric($_POST['status']))
				alert('参数不完整！','',1);
			$data = array(
				'cid'=>1,  //顶部导航
				'parentid'=>$_POST['parentid'],
				'name'=>$_POST['name'],
				'href'=>$_POST['href'],
				'sort'=>$_POST['sort'],
				'status'=>$_POST['status'],
			);
			//修改/添加菜单
			$id = empty($_POST['id']) ? $this->liaoqiu_nav_model->setRecord($data) : $this->liaoqiu_nav_model->editRecordByID($_POST['id'],$data);
			$id ? alert('添加成功','config/nav_add?id='.$id) : alert('添加失败！','',1);
		}else{
			//判断是否为修改
			if(isset($_GET['id']) && is_numeric(trim($_GET['id']))){
				$nav = $this->liaoqiu_nav_model->getRecordById($_GET['id']);
				if($nav && $nav['parentid']!=0){
					$parent = $this->liaoqiu_nav_model->getRecordById($nav['parentid']);
					$nav['parent_name'] = $parent['name'];
				}
				$this->data['nav'] = $nav;
			}
			//判断是否有顶级菜单
			if(isset($_GET['parentid']) && is_numeric(trim($_GET['parentid']))){
				$nav = $this->liaoqiu_nav_model->getRecordById($_GET['parentid']);
				$this->data['nav'] = array('parentid'=>$_GET['parentid'],'parent_name'=>$nav['name']);
			}			
			$this->show('nav_add',$this->data);
		}
	}

	//删除记录
	public function del_nav()
	{
		if(!isset($_GET['id']) && !is_numeric(trim($_GET['id']))){
				 alert('参数错误','',1);
		}
		$true = $this->liaoqiu_nav_model->delRecordById($_GET['id']);
		$true ? alert('删除成功','',1) : alert('删除失败！','',1);
	}	
		

}
?>