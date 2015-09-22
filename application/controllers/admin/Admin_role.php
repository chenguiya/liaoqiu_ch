<?php
/**
 * 角色管理和管理员管理
 *
 * @author xiaoquanji
 */

require_once __DIR__ . '/Base.php';
class Admin_role extends Base
{
    
    //put your code here
    public function __construct() 
    {
        parent::__construct();
        load_model('admin_role');
        load_model('admin');
    }
    
    /**
     * 角色管理
     * 
     */
    public function index($now_page = 1)
    {
        
        $params = array();
        $name = $this->input->get('name', true);
        $status = $this->input->get('status', true);
        
        if(!empty($name))
        {
            $params['name'] = $name;
        }
        
        if(isset($status) && ($status != ''))
        {
            $params['status'] = $status;
        }
        
        $count = $this->liaoqiu_admin_role_model->get_count($params);
        $add = '/admin/admin_role/index';
        //分页
        $page = $this->page_config($count, $add, $now_page);
        $params['start'] = ($now_page - 1) * Liaoqiu_admin_role_model::LIMIT;
        $list = $this->liaoqiu_admin_role_model->get_roles($params);
        if(!empty($list))
        {
            foreach ($list as &$row)
            {
                $row['status_msg'] = isset(Liaoqiu_admin_role_model::$status_arr[$row['status']]) ? Liaoqiu_admin_role_model::$status_arr[$row['status']] : Liaoqiu_admin_role_model::$status_arr[Liaoqiu_admin_role_model::STATUS_DELETE];
                
            }
        }
        
        $this->data['params'] = array_merge(array('status' => '', 'name' => ''), $params);
        
        $this->data['page'] = $page['page'];
        $this->data['admin_role_list'] = $list;
        
        //导航
        $nav_list = $this->get_nav_list();
        $this->data['role_nav_list'] = array();
        if(!empty($nav_list))
        {
            $this->data['role_nav_list'] = $nav_list;
        }
        $this->show('admin_role_list', $this->data);
    }
    
    /**
     * 获取权限列表
     * 
     */
    private function get_nav_list()
    {
        load_model('nav');
        $list = $this->liaoqiu_nav_model->getRecord();
        $nav_list = array();
        foreach ($list as $k => $v) 
        {
            if($v['parentid']) 
                $nav_list[$v['parentid']]['child'][] = $v;
            else 
                $nav_list[$v['id']]['parent'] = $v;
	}
        
        return $nav_list;
    }

    /**
     * 新增角色
     * 
     */
    public function add()
    {
        $return = array('state_code' => 0, 'state_desc' => '添加角色成功');
        
        try 
        {
            $params = array();
            $params['name'] = $this->input->post('name', true);
            $params['desc'] = $this->input->post('desc', true);
            $params['status'] = $this->input->post('status', true);
            $params['sort'] = $this->input->post('sort', true);
            
            if(empty($params['name']) || empty($params['desc']) || !isset($params['status']) || !isset($params['sort']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $params['sort'] = empty($params['sort']) ? 0 : $params['sort'];
            $result = $this->liaoqiu_admin_role_model->setRecord($params);
            
            if(empty($result))
            {
                throw new Exception('添加角色失败', 19024);
            }
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }
    
    /**
     * 修改角色
     * 
     */
    public function edit()
    {
        $return = array('state_code' => 0, 'state_desc' => '修改角色成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->post('id', true);
            
            $params['name'] = $this->input->post('name', true);
            $params['desc'] = $this->input->post('desc', true);
            $params['status'] = $this->input->post('status', true);
            $params['sort'] = $this->input->post('sort', true);
            
            if(empty($params['id']) || empty($params['name']) || empty($params['desc']) )
            {
                throw new Exception('参数错误', 19023);
            }
            
            $result = $this->liaoqiu_admin_role_model->editRecordByID($params['id'], $params);
            
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }

    /**
     * 删除角色
     * 
     */
    public function del()
    {
        $return = array('state_code' => 0, 'state_desc' => '删除角色成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->post('id', true);
            
            if(empty($params['id']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $params['status'] = Liaoqiu_admin_role_model::STATUS_DELETE;
            
            $result = $this->liaoqiu_admin_role_model->editRecordByID($params['id'], $params);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }

    /**
     * 管理员列表
     * 
     */
    public function manager($now_page = 1)
    {
        $params = array();
        $params['lock'] = 1;
        $count = $this->liaoqiu_admin_model->get_count ($params);
        $add = '/admin/admin_role/manager';
        //分页
        $page = $this->page_config($count, $add, $now_page);
        $params['start'] = ($now_page - 1) * Liaoqiu_admin_model::LIMIT;
        $list = $this->liaoqiu_admin_model->getList($params);
        
        $this->data['params'] = array_merge(array('status' => '', 'name' => ''), $params);
        
        //所有的角色
        $this->data['roles'] = $this->liaoqiu_admin_role_model->get_roles(array('start' => 0, 'limit' => 10000));
        
        $roles = array();
        if(!empty($this->data['roles']))
        {
            foreach ($this->data['roles'] as $role)
            {
                $roles[$role['id']] = $role['name'];
            }
        }
        if(!empty($list))
        {
            foreach ($list as &$row)
            {
                $row['role_desc'] = isset($roles[$row['role']]) ? $roles[$row['role']] : $row['role'];
            }
        }
        $this->data['page'] = $page['page'];
        $this->data['admin_list'] = $list;
        $this->show('admin_list', $this->data);
        
    }
    
    /**
     * 添加管理员
     */
    public function manager_add()
    {
        $return = array('state_code' => 0, 'state_desc' => '添加管理员成功');
        
        try 
        {
            $params = array();
            $params['username'] = $this->input->post('username', true);
            $params['password'] = $this->input->post('password', true);
            $params['re_password'] = $this->input->post('re_password', true);
            $params['name'] = $this->input->post('name', true);
            $params['role'] = $this->input->post('role', true);
            
            if(empty($params['username']) || empty($params['password']) || empty($params['name']) )
            {
                throw new Exception('参数错误', 19023);
            }
            
            if(strcmp($params['password'], $params['re_password']) !== 0)
            {
                throw new Exception('密码不一致', 19020);
            }
            $params['password'] = md5($params['password']);
            unset( $params['re_password']);
            $params['role'] = empty($params['role']) ? 1 : $params['role'];
            
            $result = $this->liaoqiu_admin_model->setRecord($params);
            
            if(empty($result))
            {
                throw new Exception('添加管理员失败', 19024);
            }
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }
    
    /**
     * 修改管理员信息
     * 
     */
    public function manager_edit()
    {
        $return = array('state_code' => 0, 'state_desc' => '修改管理员成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->post('id', true);
            
            $params['username'] = $this->input->post('username', true);
            $params['password'] = $this->input->post('password', true);
            $params['re_password'] = $this->input->post('re_password', true);
            $params['name'] = $this->input->post('name', true);
            $params['role'] = $this->input->post('role', true);
            
            if(empty($params['username']) || empty($params['name']) )
            {
                throw new Exception('参数错误', 19023);
            }
            
            if(!empty($params['password']) && (strcmp($params['password'], $params['re_password']) !== 0) )
            {
                throw new Exception('密码不一致', 19020);
            }
            else 
            {
                unset($params['password']);
            }
            unset($params['re_password']);
            
            if(empty($params['id']) || empty($params['name'])  )
            {
                throw new Exception('参数错误', 19023);
            }
            
            $result = $this->liaoqiu_admin_model->editRecordByID($params['id'], $params);
            
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }
    
    /**
     * 删除管理员
     * 
     */
    public function manager_del()
    {
        $return = array('state_code' => 0, 'state_desc' => '删除管理员成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->post('id', true);
            
            if(empty($params['id']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $params['lock'] = 2;
            
            $result = $this->liaoqiu_admin_model->editRecordByID($params['id'], $params);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }
    
    /**
     * 分配权限
     * 
     */
    public function distribute()
    {
        $return = array('state_code' => 0, 'state_desc' => '分配权限成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->post('id', true);
            
            $params['manager'] = $mangers = array_unique($this->input->post('manager', true) );
            
            if(empty($params['id']) || empty($params['manager']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            //验证权限
            $nav_list = $this->get_nav_list();
            if(!empty($nav_list))
            {
                foreach ($nav_list as $nav)
                {
                    if(empty($mangers))
                    {
                        break;
                    }
                    
                    if(in_array($nav['parent']['id'], $mangers) )
                    {
                        unset($mangers[array_search($nav['parent']['id'], $mangers)]);
                        
                    }
                    
                    foreach ($nav['child'] as $row)
                    {
                        if (in_array($row['id'], $mangers))
                        {

                            unset($mangers[array_search($row['id'], $mangers)]);
                            continue;
                        }
                    }
                }
            }
            
            if(!empty($mangers))
            {
                throw new Exception('权限分配有误', 19024);
            }
            $params['manager'] = implode(',', $params['manager']);
            
            $result = $this->liaoqiu_admin_role_model->editRecordByID($params['id'], $params);
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
    }
    
    public function get_one_role()
    {
        $return = array('state_code' => 0, 'state_desc' => '修改角色成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->get('id', true);
            if(empty($params['id']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $result = $this->liaoqiu_admin_role_model->get_one($params['id']);
            
            if(empty($result))
            {
                throw new Exception('角色不存在!', 12036);
            }
            
            $return['data'] = $result;
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->data['title'] = '修改角色';
        $this->data['role'] = $result;
        $this->show('admin_role_edit', $this->data);
        
        //$this->format_print($return);
    }
    
    public function get_one_manager()
    {
        $return = array('state_code' => 0, 'state_desc' => '修改管理员成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->get('id', true);
            if(empty($params['id']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $result = $this->liaoqiu_admin_model->get_one($params['id']);
            
            if(empty($result))
            {
                throw new Exception('管理员不存在!', 12036);
            }
            
            $return['data'] = $result;
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        //所有的角色
        load_model('admin_role');
        $this->data['roles'] = $this->liaoqiu_admin_role_model->get_roles(array('start' => 0, 'limit' => 10000));
        $this->data['title'] = '修改管理员';
        $this->data['manager'] = $result;
        $this->show('manager_edit', $this->data);
        
        //$this->format_print($return);
    }
    
    public function get_role_nav()
    {
        $return = array('state_code' => 0, 'state_desc' => '获取权限成功');
        
        try
        {
            $params = array();
            $params['id'] = $this->input->get('id', true);
            if(empty($params['id']))
            {
                throw new Exception('参数错误', 19023);
            }
            
            $result = $this->liaoqiu_admin_role_model->get_one($params['id']);
            
            if(empty($result))
            {
                throw new Exception('管理权限不存在!', 12036);
            }
            
            $result['manager'] = explode(',', $result['manager']);
            $return['data'] = $result;
        } 
        catch (Exception $ex) 
        {
            $return['state_code'] = $ex->getCode();
            $return['state_desc'] = $ex->getMessage();
        }
        
        $this->format_print($return);
        //$this->format_print($nav_list);
    }
    
    
    public function get_one_distribute()
    {
                
        //导航
        $nav_list = $this->get_nav_list();
        $this->data['role_nav_list'] = array();
        if(!empty($nav_list))
        {
            $this->data['role_nav_list'] = $nav_list;
        }
        
        $params = array();
        $params['id'] = $this->input->get('id', true);
        $result = $this->liaoqiu_admin_role_model->get_one($params['id']);
        $result['manager'] = !empty($result['manager']) ? explode(',', $result['manager']) : array();
        $this->data['distribute'] = $result;
        $this->data['title'] = '修改权限分配';
        $this->show('admin_role_distribute', $this->data);
    }
}
