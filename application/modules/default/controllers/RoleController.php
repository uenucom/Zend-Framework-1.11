<?php

/**
 * 角色管理
 */
class RoleController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (!is_null($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            RoleList::getAdmission($this->auth, $this->_request, $this->_helper); //权限控制器
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
        DB::conn('db');
    }

    /**
     * 角色列表
     */
    public function indexAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $currentPage = trim($filter->filter($this->_request->getPost('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getPost('numPerPage', 20)));
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
        } else {
            $currentPage = trim($filter->filter($this->_request->getParam('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getParam('numPerPage', 20)));
            $role_name = trim($filter->filter($this->_request->getParam('role_name')));
        }
        $rolelist = RoleList::getRoleListByPage($currentPage, $numPerPage, $role_name);
        $this->view->rolelist = $rolelist;
        $this->view->role_name = $role_name;
    }

    /**
     * 添加角色
     */
    public function addAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
            $role_intro = trim($filter->filter($this->_request->getPost('role_intro')));
            $enable = trim($filter->filter($this->_request->getPost('enable')));
            $resource = $this->_request->getPost('resource');
            $enable = $this->_request->getPost('enable');
            $role_menu = array();
            if (isset($resource)) {
                $acl = new Zend_Acl();
                $roleAdmin = new Zend_Acl_Role($role_name);
                $acl->addRole($roleAdmin);
                if (count($resource)) {
                    for ($a = 0; $a < count($resource); $a++) {
                        $c_resource = $resource[$a];
                        $actionlist = $this->_request->getPost($c_resource); //actionlist
                        if (isset($actionlist)) {
                            if (count($actionlist)) {
                                for ($c = 0; $c < count($actionlist); $c++) {
                                    $role_menu[] = $c_resource . '_' . $actionlist[$c];
                                }
                                //添加资源
                                $acl->add(new Zend_Acl_Resource($c_resource));
                                $acl->allow($role_name, $c_resource, $actionlist);
                            }
                        }
                    }
                } else {
                    $acl->deny($role_name, null, null); //收回删除权限
                }
                $role_acl = serialize($acl);
            }

            $set = array(
                'role_name' => $role_name,
                'role_intro' => $role_intro,
                'enable' => $enable,
                'create_time' => date("Y-m-d H:i:s"),
            );
            if (isset($role_acl)) {
                $set['role_acl'] = $role_acl;
            }
            if (count($role_menu)) {
                $set['role_menu'] = serialize($role_menu);
            }
            RoleList::addRoleInfo($set);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'role_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
        } else {
            $this->view->extlist = RoleList::getExtList();
            $this->view->acllist = MenuInit::doReflectionMethod();
        }
    }

    public function updateAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $role_id = trim($filter->filter($this->_request->getPost('role_id'))); //getPost
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
            $role_intro = trim($filter->filter($this->_request->getPost('role_intro')));
            $enable = trim($filter->filter($this->_request->getPost('enable')));
            $resource = $this->_request->getPost('resource');
            $enable = $this->_request->getPost('enable');
            $role_menu = array();
            if (isset($resource)) {
                $acl = new Zend_Acl();
                $roleAdmin = new Zend_Acl_Role($role_name);
                $acl->addRole($roleAdmin);
                if (count($resource)) {
                    for ($a = 0; $a < count($resource); $a++) {
                        $c_resource = $resource[$a];
                        $actionlist = $this->_request->getPost($c_resource); //actionlist
                        if (isset($actionlist)) {
                            if (count($actionlist)) {
                                for ($c = 0; $c < count($actionlist); $c++) {
                                    $role_menu[] = $c_resource . '_' . $actionlist[$c];
                                }
                                //添加资源
                                $acl->add(new Zend_Acl_Resource($c_resource));
                                $acl->allow($role_name, $c_resource, $actionlist);
                            }
                        }
                    }
                } else {
                    $acl->deny($role_name, null, null); //收回删除权限
                }
                $role_acl = serialize($acl);
            } else {
                if (trim($role_name) === 'admin') {
                    $acl = new Zend_Acl();
                    $roleAdmin = new Zend_Acl_Role('admin');
                    $acl->addRole($roleAdmin);
                    $acl->allow($roleAdmin, null, null);
                    $role_acl = serialize($acl);
                }
            }

            $set = array(
                'role_name' => $role_name,
                'role_intro' => $role_intro,
                'enable' => $enable,
                'create_time' => date("Y-m-d H:i:s"),
            );
            if (isset($role_acl)) {
                $set['role_acl'] = $role_acl;
            }
            if (count($role_menu)) {
                $set['role_menu'] = serialize($role_menu);
            }
            RoleList::updateRoleInfoById($set, $role_id);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'role_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
            exit();
        } else {
            $role_id = trim($filter->filter($this->_request->getParam('role_id')));
            $roleresult = RoleList::getRoleInfoById($role_id);
            $this->view->roleinfo = $roleresult;
            if (trim($roleresult['role_menu']) !== '') {
                $this->view->mlist = unserialize($roleresult['role_menu']);
            } else {
                $this->view->mlist = array();
            }
            $this->view->extlist = RoleList::getExtList($roleresult['role_name']);
            $this->view->acllist = MenuInit::doReflectionMethod($roleresult['role_name']);

            $this->view->role_id = $role_id;
        }
    }

    /**
     * 删除角色
     */
    public function delAction() {
        echo "del";
    }

    /**
     * 检查角色
     */
    public function checkAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $role_name = trim($filter->filter($this->_request->getParam('role_name')));
        $role_id = trim($filter->filter($this->_request->getParam('u')));
        if ($role_id) {
            $num = RoleList::checkURole($role_name, $role_id);
        } else {
            $num = RoleList::checkRole($role_name);
        }
        if ($num) {
            echo "false";
        } else {
            echo "true";
        }
    }

}
