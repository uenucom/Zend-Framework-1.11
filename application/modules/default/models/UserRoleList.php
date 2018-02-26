<?php

class UserRoleList {

    //获取所有用户
    public static function getAllUserList() {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info', array('user_id', 'user_name', 'user_realname','user_mdate'));
        $result = $DB->fetchAll($select->__toString());
        return $result;
    }

    //权限
    public static function getRoleList() {
        $list = self::getAllUserList();
        $acllist = MenuInit::doReflectionMethod('op');
        foreach ($list as &$arr) {
            $uid = $arr['user_id'];
            $arr['acl'] = self::getRoleTxtById($acllist, $uid);
            //echo $uid;print_r($arr['acl']);exit();
        }
        return $list;
    }

    public static function getRoleTxtById($acllist, $user_id) {
        $userinfo = UserList::getUserInfoById($user_id);
        if ($userinfo['role_name'] == '') {
            $userinfo['role_name'] = $userinfo['user_name'] . "_role";
        }
        if (trim($userinfo['role_menu']) !== '') {
            $mlist = unserialize($userinfo['role_menu']);
        } else {
            $mlist = array();
        }
        //$this->view->acllist = MenuInit::doReflectionMethod($userinfo['role_name']);
        foreach ($acllist as $key => &$val) {
            if (isset($val['action'])) {
                $n = 0;
                foreach ($val['action'] as $subkey => &$subval) {
                    if (in_array($val['resource'] . '_' . $subval['action'], $mlist)) {
                        $n = 1;
                    } else {
                        unset($acllist[$key]['action'][$subkey]);
                    }
                }
            }
            if ($n) {
                $val['show'] = 1;
            } else {
                $val['show'] = 0;
                unset($acllist[$key]);
            }
        }
        $str = '';
        foreach ($acllist as $key => $val) {
            if (trim($val['title']) !== '') {
                $str .=trim($val['title']) . ":";
            } else {
                $str .=trim($val['resource']) . ":";
            }
            if (isset($val['action'])) {
                $n = 0;
                $str .= "\t";
                foreach ($val['action'] as $subkey => &$subval) {
                    if (in_array($val['resource'] . '_' . $subval['action'], $mlist)) {
                        if ($n == 0) {
                            if (trim($subval['title']) !== '') {
                                $str .= trim($subval['title']);
                            } else {
                                //$str .= trim($subval['action']);
                                $str .= ' ';
                            }
                        } else {
                            if (trim($subval['title']) !== '') {
                                $str .= "、" . trim($subval['title']);
                            } else {
                                //$str .= "、" . trim($subval['action']);
                                //$str .= "、" . ' ';
                            }
                        }
                    }
                    $n++;
                }
            }
            $str .= "\r\n";
        }
        if ($userinfo['role_name'] !== 'admin') {
            return $str;
        } else {
            return '系统管理员';
        }
    }

}
