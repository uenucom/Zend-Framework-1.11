<?php

/**
 * 用户管理
 */
class UserController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->disableLayout(); //enableLayout
        //$this->_helper->viewRenderer->setNoRender();
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            RoleList::getAdmission($this->auth, $this->_request, $this->_helper);
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
        DB::conn('db');
    }

    /**
     * 浏览权限
     */
    public function indexAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $currentPage = trim($filter->filter($this->_request->getPost('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getPost('numPerPage', 20)));
            $user_id = trim($filter->filter($this->_request->getPost('user_id'))); //getPost
            $user_name = trim($filter->filter($this->_request->getPost('user_name')));
        } else {
            $currentPage = trim($filter->filter($this->_request->getParam('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getParam('numPerPage', 20)));
            $user_id = trim($filter->filter($this->_request->getParam('user_id'))); //getPost
            $user_name = trim($filter->filter($this->_request->getParam('user_name')));
        }
        $userlist = UserList::getListByPage($currentPage, $numPerPage, $user_id, $user_name);
        $this->view->userlist = $userlist;
        $this->view->user_id = $user_id;
        $this->view->user_name = $user_name;
    }

    /**
     * 添加
     */
    public function addAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $user_name = trim($filter->filter($this->_request->getPost('user_name')));
            $user_realname = trim($filter->filter($this->_request->getPost('user_realname')));
            $user_mobile = trim($filter->filter($this->_request->getPost('user_mobile')));
            $user_mail = trim($filter->filter($this->_request->getPost('user_mail')));
            $password1 = trim($filter->filter($this->_request->getPost('password1')));
            $password2 = trim($filter->filter($this->_request->getPost('password2')));
            $user_role = trim($filter->filter($this->_request->getPost('user_role')));
            //新增权限控制 开始
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
            if (trim($role_name) == '') {
                $role_name = $user_name . "_role";
            }
            $resource = $this->_request->getPost('resource');
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
                    $acl->deny($role_name, null, null); //收回权限
                }
                $role_acl = serialize($acl);
            } else {
                if (trim($role_name) === 'admin') {
                    $acl = new Zend_Acl();
                    $roleAdmin = new Zend_Acl_Role('admin');
                    $acl->addRole($roleAdmin);
                    $acl->allow($roleAdmin);
                    $role_acl = serialize($acl);
                }
            }
            //新增权限控制 结束
            $set = array(
                'user_name' => $user_name,
                'user_realname' => $user_realname,
                'user_mobile' => $user_mobile,
                'user_mail' => $user_mail,
                // 'user_role' => $user_role,
                'role_name' => $role_name,
                'user_status' => 1,
                'user_mdate' => date("Y-m-d H:i:s"),
                'user_type' => 3,
                'user_plugType' => ",,",
                'user_menu' => "{}",
                'user_cpType' => 0,
                'user_cpId' => 0,
            );

            if (isset($role_acl)) {
                //$set['role_acl'] = $role_acl;
            }
            if (count($role_menu)) {
                $set['role_menu'] = serialize($role_menu);
            } else {
                $set['role_menu'] = serialize(array());
            }

            if ($password1 !== '' && $password2 !== '') {
                if ($password1 != $password2) {
                    $inforesult = array();
                    $inforesult['statusCode'] = '300';
                    $inforesult['message'] = '两次输入的密码不一致';
                    $inforesult['navTabId'] = '';
                    $inforesult['rel'] = '';
                    $inforesult['callbackType'] = '';
                    $inforesult['forwardUrl'] = '';
                    $inforesult['confirmMsg'] = '';
                    echo json_encode($inforesult);
                } else {
                    $set['user_password'] = md5($password1);
                }
            }
            $user_id = UserList::addUserInfo($set);
            $r_log = array();
            $r_log['user_id'] = $user_id;
            $r_log['username'] = $user_realname;
            $r_log['org_role'] = serialize(array());
            $r_log['opname'] = $this->auth->user_realname;
            $r_log['new_role'] = serialize($role_menu);
            UserList::writeRoleLog($r_log, $role_name, true);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'user_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
        } else {
            $this->view->rolelist = RoleList::getRoleList();
            $this->view->acllist = MenuInit::doReflectionMethod();
        }
    }

    /**
     * 更新
     */
    public function updateAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $user_id = trim($filter->filter($this->_request->getPost('user_id'))); //getPost
            $user_name = trim($filter->filter($this->_request->getPost('user_name')));
            $user_realname = trim($filter->filter($this->_request->getPost('user_realname')));
            $user_mobile = trim($filter->filter($this->_request->getPost('user_mobile')));
            $user_mail = trim($filter->filter($this->_request->getPost('user_mail')));
            $password1 = trim($filter->filter($this->_request->getPost('password1')));
            $password2 = trim($filter->filter($this->_request->getPost('password2')));
            $user_status = trim($filter->filter($this->_request->getPost('user_status')));
            $user_role = trim($filter->filter($this->_request->getPost('user_role'))); //停用
            //新增权限控制 开始
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
            $resource = $this->_request->getPost('resource');
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
                    $acl->deny($role_name, null, null); //收回权限
                }
                $role_acl = serialize($acl);
            }
            if (trim($role_name) === 'admin') {
                $acl = new Zend_Acl();
                $roleAdmin = new Zend_Acl_Role('admin');
                $acl->addRole($roleAdmin);
                $acl->allow('admin');
                $role_acl = serialize($acl);
            }
            //新增权限控制 结束
            $set = array(
                'user_name' => $user_name,
                'user_realname' => $user_realname,
                'user_mobile' => $user_mobile,
                'user_mail' => $user_mail,
                'user_status' => $user_status,
                // 'user_role' => $user_role,
                'role_name' => $role_name,
            );
            if (isset($role_acl)) {
                //$set['role_acl'] = $role_acl;
            }
            if (count($role_menu)) {
                $set['role_menu'] = serialize($role_menu);
            } else {
                $set['role_menu'] = serialize(array());
            }

            if ($password1 !== '' && $password2 !== '') {
                if ($password1 != $password2) {
                    $inforesult = array();
                    $inforesult['statusCode'] = '300';
                    $inforesult['message'] = '两次输入的密码不一致';
                    $inforesult['navTabId'] = '';
                    $inforesult['rel'] = '';
                    $inforesult['callbackType'] = '';
                    $inforesult['forwardUrl'] = '';
                    $inforesult['confirmMsg'] = '';
                    echo json_encode($inforesult);
                    exit();
                } else {
                    $set['user_password'] = md5($password1);
                }
            }
            $orgrole = UserList::getOrgRoleById($user_id);
            UserList::updateUserInfoById($set, $user_id);
            $r_log = array();
            $r_log['user_id'] = $user_id;
            $userinfo = UserList::getUserInfoById($user_id);
            $r_log['username'] = $userinfo['user_realname'];
            $r_log['org_role'] = $orgrole;
            $r_log['opname'] = $this->auth->user_realname;
            $r_log['new_role'] = serialize($role_menu);
            UserList::writeRoleLog($r_log, $role_name);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'user_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
            exit();
        } else {

            $user_id = trim($filter->filter($this->_request->getParam('user_id')));
            $userinfo = UserList::getUserInfoById($user_id);
            if ($userinfo['role_name'] == '') {
                $userinfo['role_name'] = $userinfo['user_name'] . "_role";
            }
            $this->view->rolelist = RoleList::getRoleList();
            $this->view->userinfo = $userinfo;
            $this->view->user_id = $user_id;
            if (trim($userinfo['role_menu']) !== '') {
                $this->view->mlist = unserialize($userinfo['role_menu']);
            } else {
                $this->view->mlist = array();
            }
            $this->view->acllist = MenuInit::doReflectionMethod($userinfo['role_name']);
        }
    }

    /**
     * 导出excel
     */
    public function excelAction() {
        $this->_helper->viewRenderer->setNoRender();
        $filter = new Zend_Filter_StripTags();
        $user_id = trim($filter->filter($this->_request->getParam('user_id')));
        $user_name = trim($filter->filter($this->_request->getParam('user_name')));
        ob_end_clean();
        ob_start();
        require_once(LIBRARY_PATH . '/PHPExcel.php');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'UID')//作品ID
                ->setCellValue('B1', '用户名')//
                ->setCellValue('C1', '真实姓名')
                ->setCellValue('D1', '电话')
                ->setCellValue('E1', '邮箱');

        $list = UserList::getAllUserInfo();
        for ($a = 0; $a < count($list); $a++) {
            $uinfo = $list[$a];
            $m = $a + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $m, "" . $uinfo['user_id'])
                    ->setCellValue('B' . $m, "" . $uinfo['user_name'])
                    ->setCellValue('C' . $m, "" . $uinfo['user_realname'])
                    ->setCellValue('D' . $m, "" . $uinfo['user_mobile'])
                    ->setCellValue('E' . $m, "" . $uinfo['user_mdate']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        //$objPHPExcel->setActiveSheetIndex(0);
        //ob_start();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="userlist.xlsx"');
        header('Cache-Control: max-age=0');
        header("Content-Transfer-Encoding:binary");
        header("Content-Type:application/download");
        $objWriter->save('php://output');
//        $filename = str_replace('.php', '.xlsx', __FILE__);
//        if (file_exists($filename)) {
//            unlink($filename);
//        }
//        $objWriter->save($filename);
//        header("Location:" . str_replace('.php', '.xlsx', 'excelexport_userrechage.xlsx'));
        exit();
    }

    /**
     * 删除操作
     */
    public function delAction() {
        $this->_helper->viewRenderer->setNoRender();
        $filter = new Zend_Filter_StripTags();
        $user_id = trim($filter->filter($this->_request->getParam('user_id')));
        //UserList::del($user_id);
        $inforesult = array();
        $inforesult['statusCode'] = '200';
        $inforesult['message'] = '操作成功';
        $inforesult['navTabId'] = 'user_list';
        $inforesult['rel'] = '';
        $inforesult['callbackType'] = '';
        $inforesult['forwardUrl'] = '';
        $inforesult['confirmMsg'] = '';
        echo json_encode($inforesult);
    }

    /**
     * 修改密码
     */
    public function passwdAction() {
        //$this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $oldpasswd = trim($filter->filter($this->_request->getPost('oldpasswd')));
            $password1 = trim($filter->filter($this->_request->getPost('password1')));
            $password2 = trim($filter->filter($this->_request->getPost('password2')));

            $set = array();
            if ($password1 !== '' && $password2 !== '') {
                if ($password1 != $password2) {
                    $inforesult = array();
                    $inforesult['statusCode'] = '300';
                    $inforesult['message'] = '两次输入的密码不一致';
                    $inforesult['navTabId'] = '';
                    $inforesult['rel'] = '';
                    $inforesult['callbackType'] = '';
                    $inforesult['forwardUrl'] = '';
                    $inforesult['confirmMsg'] = '';
                    echo json_encode($inforesult);
                    exit();
                } else {
                    $set['user_password'] = md5($password1);
                }
            }
            $oldpasswd_val = UserList::checkPasswd($this->auth->user_id);
            if ($oldpasswd_val !== md5($oldpasswd)) {
                $inforesult = array();
                $inforesult['statusCode'] = '300';
                $inforesult['message'] = '原密码不正确';
                $inforesult['navTabId'] = '';
                $inforesult['rel'] = '';
                $inforesult['callbackType'] = '';
                $inforesult['forwardUrl'] = '';
                $inforesult['confirmMsg'] = '';
                echo json_encode($inforesult);
                exit();
            }

            UserList::updateUserInfoById($set, $this->auth->user_id);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'user_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
            exit();
        }
    }

    /**
     * 角色模板
     */
    public function roleAction() {
        //$this->_helper->viewRenderer->setNoRender();
        $filter = new Zend_Filter_StripTags();
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

    //批量查询
    public function batchsearchAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $type = trim($filter->filter($this->_request->getPost('type'))); //getPost
            $uid = trim($filter->filter($this->_request->getPost('uid'))); //getPost
            $userlist = trim($filter->filter($this->_request->getPost('userlist'))); //getPost
            $ret = UserList::batchSearch($userlist, $type, $uid);
            $this->_helper->json($ret);
        }
    }

    //选择用户
    public function subsearchAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $currentPage = trim($filter->filter($this->_request->getPost('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getPost('numPerPage', 20)));
            $user_name = trim($filter->filter($this->_request->getPost('user_name')));
            $user_id = trim($filter->filter($this->_request->getParam('user_id'))); //getPost
            //$user_id = trim($filter->filter($this->_request->getPost('orderField'))); //getPost orderDirection orderField
        } else {
            $currentPage = trim($filter->filter($this->_request->getParam('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getParam('numPerPage', 20)));
            $user_id = trim($filter->filter($this->_request->getParam('user_id'))); //getPost
            $user_name = trim($filter->filter($this->_request->getParam('user_name')));
        }
        $userlist = UserList::getListByPage($currentPage, $numPerPage, $user_id, $user_name);
        $this->view->userlist = $userlist;
        $this->view->user_id = $user_id;
        $this->view->user_name = $user_name;
    }

    /**
     * 批量授权
     */
    public function batchAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $dotype = trim($filter->filter($this->_request->getPost('dotype'))); //getPost
            $role_name = trim($filter->filter($this->_request->getPost('role_name')));
            $userlist = $this->_request->getPost('user_id');
            if (!isset($userlist) || empty($userlist)) {
                $inforesult = array();
                $inforesult['statusCode'] = '300';
                $inforesult['message'] = '请选择用户';
                $inforesult['navTabId'] = 'user_batch';
                $inforesult['rel'] = '';
                //$inforesult['callbackType'] = 'closeCurrent';
                $inforesult['forwardUrl'] = '';
                $inforesult['confirmMsg'] = '';
                $this->_helper->json($inforesult);
                exit();
            }
            $resource = $this->_request->getPost('resource');
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
                    $acl->deny($role_name, null, null); //收回权限
                }
                $role_acl = serialize($acl);
            } else {
                if (trim($role_name) === 'admin') {
                    $acl = new Zend_Acl();
                    $roleAdmin = new Zend_Acl_Role('admin');
                    $acl->addRole($roleAdmin);
                    $acl->allow($roleAdmin);
                    $role_acl = serialize($acl);
                }
            }
            $set = array();
            $set['userlist'] = $userlist;
            $set['type'] = $dotype;
            switch ($dotype) {
                case 'add':
                case 'del':
                    break;
                case 'like':
                    $user_id = trim($filter->filter($this->_request->getPost('userinfo_user_id')));
                    $user_realname = trim($filter->filter($this->_request->getPost('userinfo_user_realname')));
                    if ($user_id > 0) {
                        $set['uid'] = $user_id;
                    } else {
                        $inforesult = array();
                        $inforesult['statusCode'] = '300';
                        $inforesult['message'] = '请选择参考用户';
                        $inforesult['navTabId'] = 'user_batch';
                        $inforesult['rel'] = '';
                        //$inforesult['callbackType'] = 'closeCurrent';
                        $inforesult['forwardUrl'] = '';
                        $inforesult['confirmMsg'] = '';
                        $this->_helper->json($inforesult);
                        exit();
                    }
                    break;
            }
            if (isset($role_acl)) {
                //$set['role_acl'] = $role_acl;
            }
            if (trim($dotype) != 'like' && trim($role_name) != 'admin' && empty($role_menu)) {
                $inforesult = array();
                $inforesult['statusCode'] = '300';
                $inforesult['message'] = '请选择对应的权限';
                $inforesult['navTabId'] = 'user_batch';
                $inforesult['rel'] = '';
                //$inforesult['callbackType'] = 'closeCurrent';
                $inforesult['forwardUrl'] = '';
                $inforesult['confirmMsg'] = '';
                $this->_helper->json($inforesult);
                exit();
            }
            if ($role_name !== 'admin') {
                $set['role_name'] = $role_name;
            }

            if (count($role_menu)) {
                $set['role_menu'] = serialize($role_menu);
            } else {
                $set['role_menu'] = serialize(array());
            }
            $result = UserList::dobatch($set, $this->auth->user_realname);
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'user_batch';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = 'closeCurrent';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            $this->_helper->json($inforesult);
            exit();
            //新增权限控制 开始
        } else {
            $this->view->rolelist = RoleList::getRoleList();
            $this->view->acllist = MenuInit::doReflectionMethod();
        }
    }

}
