<?php

class UserList {

    /**
     * 根据user_name 获取用户信息
     * @param string $user_name
     * @return array
     */
    public static function getUserInfo($user_name) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', array('user_id', 'user_name', 'user_realname', 'role_name', 'role_acl', 'role_menu'))
                ->where('user_name = ?', $user_name)
                ->where('user_status = ?', 1);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    /**
     * 3月份停用
     * @param type $user_name
     * @return type
     */
    public static function getUserInfo_Old($user_name) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', array('user_id', 'user_name', 'user_realname'))
                ->joinLeft('softrpc_admin_role', 'softrpc_user_info.user_role = softrpc_admin_role.role_id', 'softrpc_admin_role.*')
                ->where('softrpc_user_info.user_name = ?', $user_name);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    /**
     * 获取所有用户数据
     * @return Array
     */
    public static function getAllUserInfo() {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', array('user_id', 'user_name', 'user_realname', 'user_mobile', 'user_cpType', 'user_mdate'));
        $result = $DB->fetchAll($select->__toString());
        return $result;
    }

    /**
     * 根据user_id获取用户信息
     * @param int $user_id
     * @return array
     */
    public static function getUserInfoById($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                //->from('softrpc_user_info', '*')
                //->joinLeft('softrpc_admin_role', 'softrpc_user_info.user_role = softrpc_admin_role.role_id', 'softrpc_admin_role.*')
                ->from('softrpc_user_info', '*')
                ->where('softrpc_user_info.user_id = ?', $user_id);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    /**
     * 根据用户id 获取用户名
     * @param int $user_id
     * @return string
     */
    public static function getUserNameByUid($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', array('user_name', 'user_realname'))
                ->where('user_id = ?', $user_id);
        $result = $DB->fetchRow($select->__toString());
        return $result['user_realname'];
    }

    /**
     * CHECK PASSWD
     * @param type $user_id
     * @return type
     */
    public static function checkPasswd($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', 'user_password')
                ->where('user_id = ?', $user_id);
        $result = $DB->fetchRow($select->__toString());
        return $result['user_password'];
    }

    /**
     * 新增用户
     * @param array $insertdata
     * @return int
     */
    public static function addUserInfo($insertdata) {
        $DB = Zend_Registry::get('db');
        $DB->insert('softrpc_user_info', $insertdata);
        $lastid = $DB->lastInsertId();
        return $lastid;
    }

    /**
     * 根据user_id删除用户
     * @param int $user_id
     */
    public static function del($user_id) {
        $DB = Zend_Registry::get('db');
        //$where = $DB->quoteInto('user_id = ?', $uid) . $DB->quoteInto('AND favorite_vid = ?', $vid);
        $where = $DB->quoteInto('user_id = ?', $user_id);
        $DB->delete('softrpc_user_info', $where);
    }

    /**
     * 编辑用户信息
     * @param array $set
     * @param int $user_id
     */
    public static function updateUserInfoById_bak($set, $user_id) {
        $DB = Zend_Registry::get('db');
        $where = $DB->quoteInto('user_id = ?', $user_id);
        $DB->update('softrpc_user_info', $set, $where);
    }

    //2015-01-27
    public static function updateUserInfoById($set, $user_id) {
        $DB = Zend_Registry::get('db');
        $where = $DB->quoteInto('user_id = ?', $user_id);
        if (is_array($set)) {
            $setlist = array();
            foreach ($set as $key => $val) {
                //echo $key.":".$val."<br />";
                $setlist[] = "{$key}='{$val}'";
            }
            $setsql = implode(',', $setlist);
        }
        if (trim($setsql) !== '') {
            $sql = "update softrpc_user_info set {$setsql} where user_id='{$user_id}'";
            $DB->query($sql);
        }
    }

    //获取原始acllist
    public static function getOrgRoleById($user_id) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', 'role_menu')
                ->where('user_id = ?', $user_id);
        $result = $DB->fetchRow($select->__toString());
        if (trim($result['role_menu']) !== '') {
            return $result['role_menu'];
        } else {
            return serialize(array());
        }
    }

    //处理菜单标签
    public static function doLogMenu($label) {
        $labelist = explode('_', $label);
        return $labelist[0] . '_' . $labelist[1];
    }

    //记录log
    public static function writeRoleOneLog($log) {
        $DB = Zend_Registry::get('db');
        $DB->insert('softrpc_user_role_log', $log);
        $lastid = $DB->lastInsertId();
        return $lastid;
    }

    //记录log
    public static function writeRoleLog($log, $role_name, $iscreate = FALSE) {
        $DB = Zend_Registry::get('db');
        if ($iscreate) {
            $log_str = '新增用户|';
        } else {
            $log_str = '';
        }
        if (in_array($role_name, array('admin'))) {
            $log_str = '设置为超级管理员';
        } else {
            $org_role = unserialize($log['org_role']);
            $new_role = unserialize($log['new_role']);
            $reduce = array_diff($org_role, $new_role);
            $add = array_diff($new_role, $org_role);
            sort($reduce);
            sort($add);
            if (count($reduce)) {
                $log_str .='禁用权限：';
                for ($a = 0; $a < count($reduce); $a++) {
                    $module_arr = explode("_", $reduce[$a]);
                    $filename = APPLICATION_PATH . "/modules/{$module_arr[0]}/configs/" . APPLICATION_ENV . "/menu.ini";
                    $acl_menu = new Zend_Config_Ini($filename, 'acllist');
                    if ($a == 0) {
                        $log_str .=trim($acl_menu->$reduce[$a]) !== '' ? $acl_menu->$reduce[$a] : $reduce[$a];
                        $lna = self::doLogMenu($reduce[$a]);
                        $ln = trim($acl_menu->$lna) !== '' ? $acl_menu->$lna : $lna;
                        $log_str .="({$ln})";
                    } else {
                        $log_str .=',' . (trim($acl_menu->$reduce[$a]) !== '' ? $acl_menu->$reduce[$a] : $reduce[$a]);
                        $lna = self::doLogMenu($reduce[$a]);
                        $ln = trim($acl_menu->$lna) !== '' ? $acl_menu->$lna : $lna;
                        $log_str .="({$ln})";
                    }
                }
            }
            if (count($reduce) && count($add)) {
                $log_str .='|';
            }
            if (count($add)) {
                $log_str .='开通权限：';
                for ($b = 0; $b < count($add); $b++) {
                    $module_arr = explode("_", $add[$b]);
                    $filename = APPLICATION_PATH . "/modules/{$module_arr[0]}/configs/" . APPLICATION_ENV . "/menu.ini";
                    $acl_menu = new Zend_Config_Ini($filename, 'acllist');
                    if ($b == 0) {
                        $log_str .=trim($acl_menu->$add[$b]) !== '' ? $acl_menu->$add[$b] : $add[$b];
                        $lna = self::doLogMenu($add[$b]);
                        $ln = trim($acl_menu->$lna) !== '' ? $acl_menu->$lna : $lna;
                        $log_str .="({$ln})";
                    } else {
                        $log_str .=',' . (trim($acl_menu->$add[$b]) !== '' ? $acl_menu->$add[$b] : $add[$b]);
                        $lna = self::doLogMenu($add[$b]);
                        $ln = trim($acl_menu->$lna) !== '' ? $acl_menu->$lna : $lna;
                        $log_str .="({$ln})";
                    }
                }
            }
        }
        Tools::writeLog($log_str);
        $log['log'] = $log_str;
        if (trim($log_str) !== '') {
            unset($log['org_role']);
            unset($log['new_role']);
            $DB->insert('softrpc_user_role_log', $log);
            $lastid = $DB->lastInsertId();
            return $lastid;
        }
        //softrpc_user_role_log
    }

    /**
     * 获取用户list
     * @param int $currentPage
     * @param int $numPerPage
     * @param int $user_id
     * @param string $user_name
     * @return array
     */
    public static function getListByPage($currentPage, $numPerPage, $user_id, $user_name) {
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $listresult = array();
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
        //->joinLeft('softrpc_admin_role', 'softrpc_user_info.user_role = softrpc_admin_role.role_id', 'softrpc_admin_role.*');
        if ($user_id > 0) {
            $select = $select->where('user_id = ?', $user_id);
        }
        if ($user_name !== '') {
            $select = $select->where('user_name like ?', "%{$user_name}%")
                    ->orwhere('user_realname like ?', "%{$user_name}%");
        }
        $select = $select->order('user_id DESC')
                ->limitPage($currentPage, $numPerPage);
        //echo $select->__toString();exit();
        $result = $DB->fetchAll($select->__toString());

        $numsql = 'SELECT FOUND_ROWS() as countnum';
        $num = $DB->fetchRow($numsql);
        $totalpagenum = ceil($num['countnum'] / $numPerPage);

        if ($currentPage > $totalpagenum) {
            $currentPage = $totalpagenum;
            $select = $DB->select()
                    ->from('softrpc_user_info', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
            //->joinLeft('softrpc_admin_role', 'softrpc_user_info.user_role = softrpc_admin_role.role_id', 'softrpc_admin_role.*');
            if ($user_id > 0) {
                $select = $select->where('user_id = ?', $user_id);
            }
            if ($user_name !== '') {
                $select = $select->where('user_name like ?', "%{$user_name}%")
                        ->orwhere('user_realname like ?', "%{$user_name}%");
            }
            $select = $select->order('user_id DESC')
                    ->limitPage($currentPage, $numPerPage);
            $result = $DB->fetchAll($select->__toString());
        }
        $listresult['totalCount'] = $num['countnum']; //numPerPage pageNumShown currentPage
        $listresult['numPerPage'] = $numPerPage;
        $listresult['currentPage'] = $currentPage;
        $listresult['list'] = $result;
        return $listresult;
    }

    public static function excel() {
        
    }

    //批量授权
    public static function batchSearch($userlist, $type, $uid) {
        if (!empty($userlist)) {
            $userlist = explode("\n", $userlist);
            $DB = Zend_Registry::get('db');
            $select = $DB->select()
                    ->from('softrpc_user_info', array('user_id', 'user_name', 'user_realname'));
            $sqlarr = array();
            for ($a = 0; $a < count($userlist); $a++) {
                if (trim($userlist[$a]) !== '') {
                    //$select = $select->orWhere('user_name like ?', "%{$userlist[$a]}%")->orWhere('user_realname like ?', "%{$userlist[$a]}%");
                    $sqlarr[] = "user_name like '%{$userlist[$a]}%' or user_realname like '%{$userlist[$a]}%'";
                }
            }
            $sqlstr = implode(" or ", $sqlarr);
            if (trim($type) === 'like') {
                $select = $select->where('user_id != ?', $uid);
            }
            $select = $select->where($sqlstr);
            //echo $select->__toString();exit();
            $result = $DB->fetchAll($select->__toString());
            return $result;
        } else {
            return array();
        }
    }

    //批量授权
    public static function dobatch($set, $opname) {
        $dotype = $set['type'];
        $role_menu = $set['role_menu'];
        $userlist = $set['userlist'];
        switch ($dotype) {
            case 'add':
                $setdata = array();
                //$setdata['role_menu'] = $role_menu;
                for ($a = 0; $a < count($userlist); $a++) {
                    $user_id = $userlist[$a];
                    $orgrole = UserList::getOrgRoleById($user_id);
                    $org_userinfo = UserList::getUserInfoById($user_id);
                    if ($org_userinfo['role_name'] == 'admin') {
                        //$setdata['role_name'] = 'admin';
                    } else {
                        $setdata['role_menu'] = serialize(array_merge(unserialize($orgrole), unserialize($role_menu)));
                        //print_r($setdata);exit();
                        UserList::updateUserInfoById($setdata, $user_id);
                        $r_log = array();
                        $r_log['user_id'] = $user_id;
                        $r_log['username'] = $org_userinfo['user_realname'];
                        $r_log['org_role'] = $orgrole;
                        $r_log['opname'] = $opname;
                        $r_log['new_role'] = $setdata['role_menu'];
                        UserList::writeRoleLog($r_log, $org_userinfo['role_name']);
                    }
                }
                break;
            case 'del':
                $setdata = array();
                //$setdata['role_menu'] = $role_menu;
                for ($a = 0; $a < count($userlist); $a++) {
                    $user_id = $userlist[$a];
                    $orgrole = UserList::getOrgRoleById($user_id);
                    $org_userinfo = UserList::getUserInfoById($user_id);
                    if ($org_userinfo['role_name'] == 'admin') {
                        //$setdata['role_name'] = 'admin';
                    } else {
                        $setdata['role_menu'] = serialize(array_diff(unserialize($orgrole), unserialize($role_menu)));
                        //print_r($setdata);exit();
                        UserList::updateUserInfoById($setdata, $user_id);
                        $r_log = array();
                        $r_log['user_id'] = $user_id;
                        $r_log['username'] = $org_userinfo['user_realname'];
                        $r_log['org_role'] = $orgrole;
                        $r_log['opname'] = $opname;
                        $r_log['new_role'] = $setdata['role_menu'];
                        UserList::writeRoleLog($r_log, $org_userinfo['role_name']);
                    }
                }
                break;
            case 'like':
                $uid = $set['uid'];
                if (!empty($userlist) && $uid > 0) {
                    $userinfo = UserList::getUserInfoById($uid);
                    $setdata = array();
                    //print_r($setdata);exit();
                    for ($a = 0; $a < count($userlist); $a++) {
                        $user_id = $userlist[$a];
                        $orgrole = UserList::getOrgRoleById($user_id);
                        $org_userinfo = UserList::getUserInfoById($user_id);
                        if ($userinfo['role_name'] == 'admin') {
                            $setdata['role_name'] = 'admin';
                        } else {
                            $setdata['role_name'] = $orgrole['user_name'] . '_role';
                            $setdata['role_menu'] = $userinfo['role_menu'];
                        }
                        UserList::updateUserInfoById($setdata, $user_id);
                        //print_r($orgrole);exit();
                        $r_log = array();
                        $r_log['user_id'] = $user_id;
                        $r_log['username'] = $org_userinfo['user_realname'];
                        $r_log['org_role'] = $orgrole;
                        $r_log['opname'] = $opname;
                        if ($userinfo['role_name'] == 'admin') {
                            UserList::writeRoleLog($r_log, 'admin');
                        } else {
                            $r_log['new_role'] = $setdata['role_menu'];
                            UserList::writeRoleLog($r_log, $org_userinfo['role_name']);
                        }
                    }
                    //print_r($userinfo);
                }

                break;
        }
    }

}
