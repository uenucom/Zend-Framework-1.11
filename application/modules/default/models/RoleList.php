<?php

class RoleList {

    /**
     * 获取角色列表
     */
    public static function getRoleListByPage($currentPage, $numPerPage, $role_name) {
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if (intval($numPerPage) < 1) {
            $numPerPage = 20;
        }
        $listresult = array();
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_admin_role', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
        if ($role_name !== '') {
            $select = $select->where('role_name like ?', "%{$role_name}%");
        }
        $select = $select->order('role_id DESC')
                ->limitPage($currentPage, $numPerPage);
        $result = $DB->fetchAll($select->__toString());

        $numsql = 'SELECT FOUND_ROWS() as countnum';
        $num = $DB->fetchRow($numsql);
        $totalpagenum = ceil($num['countnum'] / $numPerPage);

        if ($currentPage > $totalpagenum) {
            $currentPage = $totalpagenum;
            $select = $DB->select()
                    ->from('softrpc_admin_role', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));

            if ($role_name !== '') {
                $select = $select->where('role_name like ?', "%{$role_name}%");
            }
            $select = $select->order('role_id DESC')
                    ->limitPage($currentPage, $numPerPage);
            $result = $DB->fetchAll($select->__toString());
        }
        $listresult['totalCount'] = $num['countnum']; //numPerPage pageNumShown currentPage
        $listresult['numPerPage'] = $numPerPage;
        $listresult['currentPage'] = $currentPage;
        $listresult['list'] = $result;
        return $listresult;
    }

    //根据角色获取访问权限 2015-01-27
    public static function getAdmission($auth, $request, $helper) {
        if (!isset($_SERVER["HTTP_REFERER"]) || !strstr($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"])) {
            $helper->layout()->enableLayout();
        }
        if (isset($auth) && $auth->user_id > 0 && $auth->user_name != "") {
            if (trim($auth->role_name) === '') {
                $auth->role_name = 'guest';
            }
            if (trim($auth->role_menu) !== '') {
                if ($auth->role_name !== 'admin') {
                    $acl_list = unserialize($auth->role_menu);
                    $q_acl = strtolower(trim($request->getModuleName()) . '_' . trim($request->getControllerName()) . '_' . trim($request->getActionName()));
                    $showacl = in_array($q_acl, $acl_list) ? "1" : "0";
                    //$acl = unserialize($auth->role_acl);
                    //$showacl = $acl->isAllowed($auth->role_name, strtolower($request->getModuleName() . '_' . $request->getControllerName()), strtolower($request->getActionName())) ? "1" : "0";
                } else {
                    $showacl = 1;
                }
                if (!$showacl) {
                    $helper->layout()->disableLayout();
                    $helper->viewRenderer->setNoRender(true);
                    $inforesult = array();
                    $inforesult['statusCode'] = '403';
                    $inforesult['message'] = '你没有访问权限, 请与管理员联系。';
                    $inforesult['navTabId'] = '';
                    $inforesult['rel'] = '';
                    $inforesult['callbackType'] = 'closeCurrent';
                    $inforesult['forwardUrl'] = '';
                    header("content-type:text/html;charset=utf-8");
                    if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                        echo json_encode($inforesult);
                        exit();
                    } else {
                        exit($inforesult['message']);
                    }
                }
            } else {
                $helper->layout()->disableLayout();
                $helper->viewRenderer->setNoRender(true);
                $inforesult = array();
                $inforesult['statusCode'] = '300';
                $inforesult['message'] = '你没有访问权限, 请与管理员联系';
                $inforesult['navTabId'] = '';
                $inforesult['rel'] = '';
                $inforesult['callbackType'] = 'closeCurrent'; //closeCurrent
                $inforesult['forwardUrl'] = '';
                if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                    echo json_encode($inforesult);
                    exit();
                } else {
                    exit($inforesult['message']);
                }
            }
        } else {
            $helper->layout()->disableLayout();
            $helper->viewRenderer->setNoRender(true);
            $inforesult = array();
            $inforesult['statusCode'] = '301';
            $inforesult['message'] = '会话超时，请重新登录';
            $inforesult['navTabId'] = '';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = ''; //closeCurrent
            $inforesult['forwardUrl'] = '/';
            if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                echo json_encode($inforesult);
                exit();
            } else {
                exit($inforesult['message']);
            }
        }
    }

    //根据角色获取访问权限
    public static function getAdmissionBak($auth, $request, $helper) {
        if (!isset($_SERVER["HTTP_REFERER"]) || !strstr($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"])) {
            $helper->layout()->enableLayout();
        }
        if (isset($auth) && $auth->user_id > 0 && $auth->user_name != "") {
            if (trim($auth->role_name) === '') {
                $auth->role_name = 'guest';
            }
            if (trim($auth->role_acl) !== '') {
                if ($auth->role_name !== 'admin') {
                    $acl = unserialize($auth->role_acl);
                    $showacl = $acl->isAllowed($auth->role_name, strtolower($request->getModuleName() . '_' . $request->getControllerName()), strtolower($request->getActionName())) ? "1" : "0";
                } else {
                    $showacl = 1;
                }
                if (!$showacl) {
                    $helper->layout()->disableLayout();
                    $helper->viewRenderer->setNoRender(true);
                    $inforesult = array();
                    $inforesult['statusCode'] = '403';
                    $inforesult['message'] = '你没有访问权限, 请与管理员联系。';
                    $inforesult['navTabId'] = '';
                    $inforesult['rel'] = '';
                    $inforesult['callbackType'] = 'closeCurrent'; //closeCurrent closeCurrentTab
                    $inforesult['forwardUrl'] = '';
                    header("content-type:text/html;charset=utf-8");
                    if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                        echo json_encode($inforesult);
                        exit();
                    } else {
                        exit($inforesult['message']);
                    }
                }
            } else {
                $helper->layout()->disableLayout();
                $helper->viewRenderer->setNoRender(true);
                $inforesult = array();
                $inforesult['statusCode'] = '300';
                $inforesult['message'] = '你没有访问权限, 请与管理员联系';
                $inforesult['navTabId'] = '';
                $inforesult['rel'] = '';
                $inforesult['callbackType'] = 'closeCurrent'; //closeCurrent
                $inforesult['forwardUrl'] = '';
                if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                    echo json_encode($inforesult);
                    exit();
                } else {
                    exit($inforesult['message']);
                }
            }
        } else {
            $helper->layout()->disableLayout();
            $helper->viewRenderer->setNoRender(true);
            $inforesult = array();
            $inforesult['statusCode'] = '301';
            $inforesult['message'] = '会话超时，请重新登录';
            $inforesult['navTabId'] = '';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = ''; //closeCurrent
            $inforesult['forwardUrl'] = '/';
            if (preg_match("/application\/json/i", $_SERVER['HTTP_ACCEPT'])) {
                echo json_encode($inforesult);
                exit();
            } else {
                exit($inforesult['message']);
            }
        }
    }

    public static function doLogOut($this) {
        //$helper->layout()->disableLayout();
        //$helper->viewRenderer->setNoRender(true);
//        $inforesult = array();
//        $inforesult['statusCode'] = '301';
//        $inforesult['message'] = '会话超时，请重新登录';
//        $inforesult['navTabId'] = '';
//        $inforesult['rel'] = '';
//        $inforesult['callbackType'] = ''; //closeCurrent
//        $inforesult['forwardUrl'] = '/';
//        echo json_encode($inforesult);
        $this->_forward('index', "member", "default", array($params = null));
        exit();
    }

    /**
     * doForward
     */
    public static function doForward($obj, $request) {
        if (!isset($_SERVER["HTTP_REFERER"]) || !strstr($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"])) {
            // $obj->_helper->layout()->enableLayout();
            if (strtolower($request->getActionName()) == 'index') {
                $obj->getResponse()->setRedirect('/member', 301);
            }
        } else {
            if (strtolower($request->getActionName()) !== 'index' && strtolower($request->getControllerName()) !== 'index') {
                //$obj->_helper->viewRenderer->setNoRender();
                $inforesult = array();
                $inforesult['statusCode'] = '301';
                $inforesult['message'] = '会话超时，请重新登录';
                $inforesult['navTabId'] = '';
                $inforesult['rel'] = '';
                $inforesult['callbackType'] = '';
                $inforesult['forwardUrl'] = '/member';
                echo json_encode($inforesult);
                exit();
            } else {
                $obj->getResponse()->setRedirect('/member', 301);
            }
        }
    }

    //获取角色list
    public static function getRoleList() {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_admin_role', array('role_id', 'role_name', 'role_intro'))
                ->where('enable = ?', 1)
                ->order('role_id ASC');
        $result = $DB->fetchAll($select->__toString());
        return $result;
    }

    /**
     * 获取某一角色
     * @param int $role_id
     * @return array
     */
    public static function getRoleInfoById($role_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_admin_role', '*')
                ->where('role_id = ?', $role_id);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    /**
     * 更新角色操作
     * @param array $set
     * @param int $role_id
     */
    public static function updateRoleInfoById($set, $role_id) {
        $DB = Zend_Registry::get('db');
        $where = $DB->quoteInto('role_id = ?', $role_id);
        $DB->update('softrpc_admin_role', $set, $where);
    }

    /**
     * 新增角色
     * @param array $insertdata
     * @return int
     */
    public static function addRoleInfo($insertdata) {
        $DB = Zend_Registry::get('db');
        $DB->insert('softrpc_admin_role', $insertdata);
        $lastid = $DB->lastInsertId();
        return $lastid;
    }

    /**
     * 
     * @param string $role_name
     * @return integer
     */
    public static function checkRole($role_name) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_admin_role', 'count(*) as num')
                ->where('role_name = ?', $role_name);
        $result = $DB->fetchRow($select->__toString());
        return $result['num'];
    }

    /**
     * 
     * @param string $role_name
     * @param int $role_id
     * @return int
     */
    public static function checkURole($role_name, $role_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_admin_role', 'count(*) as num')
                ->where('role_name = ?', $role_name)
                ->where('role_id != ?', $role_id);
        $result = $DB->fetchRow($select->__toString());
        return $result['num'];
    }

    /**
     * 根据$role_id删除
     * @param int $role_id
     */
    public static function del($role_id) {
        $DB = Zend_Registry::get('db');
        //$where = $DB->quoteInto('user_id = ?', $uid) . $DB->quoteInto('AND favorite_vid = ?', $vid);
        $where = $DB->quoteInto('role_id = ?', $role_id);
        $DB->delete('softrpc_admin_role', $where);
    }

    //获取继承list
    public static function getExtList($role_name = '') {
        if (trim($role_name) !== '') {
            if (in_array($role_name, array('admin', 'guest'))) {
                return array();
            } else {
                $new_role_name = array();
                $new_role_name[] = 'admin';
                $new_role_name[] = $role_name;
            }
            $DB = Zend_Registry::get('db');
            //role_id  role_name   role_intro
            $select = $DB->select()
                    ->from('softrpc_admin_role', array('role_id', 'role_name', 'role_intro'))
                    ->where('role_name not in (?)', $new_role_name);
            $result = $DB->fetchAll($select->__toString());
            return $result;
        } else {
            $new_role_name = array();
            $new_role_name[] = 'admin';
            $DB = Zend_Registry::get('db');
            //role_id  role_name   role_intro
            $select = $DB->select()
                    ->from('softrpc_admin_role', array('role_id', 'role_name', 'role_intro'))
                    ->where('role_name not in (?)', $new_role_name);
            $result = $DB->fetchAll($select->__toString());
            return $result;
        }
    }

    /****************** 以下暂停使用 ************************ */
    public static function getInfo($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', '*')
                ->where('user_id = ?', $user_id);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    public static function getRole($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_userdolog_info', '*')
                ->where('userdolog_personid = ?', $user_id);
        $result = $DB->fetchAll($select->__toString());
        return $result;
    }

}
